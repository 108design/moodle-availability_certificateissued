<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * Native certificate state and issuance regression cases.
 * @package    availability_certificateissued
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_certificateissued;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/backup/util/loggers/output_text_logger.class.php');

/** Native state fixtures for the availability and Course certificate consumers. */
final class condition_test extends \advanced_testcase {
    /** @var \stdClass Test course. */
    private $course;
    /** @var \stdClass Enrolled learner. */
    private $learner;
    /** @var \tool_certificate\template Native template. */
    private $template;
    /** @var \core_availability\info_module Availability context. */
    private $info;

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $this->learner = $this->getDataGenerator()->create_and_enrol($this->course, 'student');
        $generator = $this->getDataGenerator()->get_plugin_generator('tool_certificate');
        $this->template = $generator->create_template((object)['name' => 'Old award', 'shared' => 1]);
        $page = $this->getDataGenerator()->create_module('page', ['course' => $this->course->id]);
        $cm = get_fast_modinfo($this->course)->get_cm($page->cmid);
        $this->info = new \core_availability\info_module($cm);
        \availability_certificateissued\local\selection::prepare(\context_course::instance($this->course->id));
    }

    /**
     * @param array $changes Changes to the default exclusion
     * @return \stdClass Condition structure
     */
    private function settings(array $changes = []): \stdClass {
        $settings = (object)array_merge([
            'type' => 'certificateissued', 'templateid' => (int)$this->template->get_id(),
            'site' => condition::site_key(), 'received' => false,
            'scope' => 'course', 'validity' => 'all', 'archive' => 'all',
            'boundcourse' => (int)$this->course->id,
        ], $changes);
        $settings->binding = \availability_certificateissued\local\selection::signature(
            $settings->templateid, $settings->boundcourse, $settings->scope);
        return $settings;
    }

    /**
     * @param array $changes Native issue field changes
     * @return int Native issue ID
     */
    private function issue(array $changes = []): int {
        global $DB;
        return $DB->insert_record('tool_certificate_issues', (object)array_merge([
            'userid' => $this->learner->id, 'templateid' => $this->template->get_id(),
            'code' => bin2hex(random_bytes(12)), 'emailed' => 0, 'timecreated' => time() - 100,
            'expires' => null, 'data' => '{}', 'component' => 'mod_coursecertificate',
            'courseid' => $this->course->id, 'archived' => 0,
        ], $changes));
    }

    public function test_exclusion_and_parent_not_use_existing_issue(): void {
        $condition = new condition($this->settings());
        $this->assertTrue($condition->is_available(false, $this->info, false, $this->learner->id));
        $this->assertFalse($condition->is_available(true, $this->info, false, $this->learner->id));
        $this->issue();
        $this->assertFalse($condition->is_available(false, $this->info, false, $this->learner->id));
        $this->assertTrue($condition->is_available(true, $this->info, false, $this->learner->id));
        $positive = new condition($this->settings(['received' => true]));
        $this->assertTrue($positive->is_available(false, $this->info, false, $this->learner->id));
        $this->assertFalse($positive->is_available(true, $this->info, false, $this->learner->id));
    }

    public function test_issue_does_not_match_another_user_template_or_course(): void {
        $otheruser = $this->getDataGenerator()->create_user();
        $othercourse = $this->getDataGenerator()->create_course();
        $other = $this->getDataGenerator()->get_plugin_generator('tool_certificate')->create_template(
            (object)['name' => 'Different award']);
        $this->issue(['userid' => $otheruser->id]);
        $this->issue(['templateid' => $other->get_id()]);
        $this->issue(['courseid' => $othercourse->id]);
        $this->issue(['courseid' => null, 'component' => 'tool_certificate']);
        $condition = new condition($this->settings());
        $this->assertTrue($condition->is_available(false, $this->info, true, $this->learner->id));
        $global = new condition($this->settings(['scope' => 'any']));
        $this->assertFalse($global->is_available(false, $this->info, true, $this->learner->id));
    }

    public function test_manual_and_other_component_issues_count_with_matching_scope(): void {
        $this->issue(['component' => 'local_certrelease']);
        $condition = new condition($this->settings());
        $this->assertFalse($condition->is_available(false, $this->info, false, $this->learner->id));
    }

    public function test_course_section_uses_the_same_predicate(): void {
        $section = get_fast_modinfo($this->course)->get_section_info(0);
        $info = new \core_availability\info_section($section);
        $condition = new condition($this->settings());
        $this->assertTrue($condition->is_available(false, $info, false, $this->learner->id));
        $this->issue();
        $this->assertFalse($condition->is_available(false, $info, false, $this->learner->id));
    }

    public function test_expiry_filters_match_native_boundary_and_never_expiring_issues(): void {
        global $DB;
        $clock = $this->mock_clock_with_frozen(1800000000);
        foreach ([null, 0, 1799999999, 1800000000, 1800000001] as $expires) {
            $DB->delete_records('tool_certificate_issues');
            $this->issue(['expires' => $expires]);
            $expired = $expires !== null && $expires > 0 && $expires < $clock->time();
            foreach (['all' => true, 'valid' => !$expired, 'expired' => $expired] as $state => $expected) {
                $condition = new condition($this->settings(['received' => true, 'validity' => $state]));
                $this->assertSame($expected, $condition->is_available(false, $this->info, false,
                    $this->learner->id), 'expires=' . var_export($expires, true) . ',state=' . $state);
            }
        }
    }

    public function test_archive_and_validity_filters_are_independent_and_match_one_issue(): void {
        $this->mock_clock_with_frozen(1800000000);
        $this->issue(['expires' => 1799999999, 'archived' => 0]);
        $this->issue(['expires' => 1800000001, 'archived' => 1]);
        $expected = [
            'all' => ['all' => true, 'current' => true, 'archived' => true],
            'valid' => ['all' => true, 'current' => false, 'archived' => true],
            'expired' => ['all' => true, 'current' => true, 'archived' => false],
        ];
        foreach ($expected as $validity => $archives) {
            foreach ($archives as $archive => $matches) {
                foreach ([true, false] as $received) {
                    $condition = new condition($this->settings(compact('validity', 'archive', 'received')));
                    $this->assertSame($matches === $received,
                        $condition->is_available(false, $this->info, false, $this->learner->id));
                }
            }
        }
    }

    public function test_revoking_removes_the_native_issue_from_the_predicate(): void {
        global $DB;
        $id = $this->issue(['archived' => 1, 'expires' => 1]);
        $condition = new condition($this->settings());
        $this->assertFalse($condition->is_available(false, $this->info, false, $this->learner->id));
        $DB->delete_records('tool_certificate_issues', ['id' => $id]);
        $this->assertTrue($condition->is_available(false, $this->info, false, $this->learner->id));
    }

    public function test_invalid_references_never_grant_access_even_when_negated(): void {
        global $DB;
        $this->issue();
        foreach ([['templateid' => 0], ['templateid' => 987654321], ['site' => str_repeat('a', 64)]] as $changes) {
            foreach ([true, false] as $received) {
                $condition = new condition($this->settings($changes + ['received' => $received]));
                foreach ([true, false] as $not) {
                    $this->assertFalse($condition->is_available($not, $this->info, false, $this->learner->id));
                }
            }
        }
        $condition = new condition($this->settings());
        $DB->delete_records('tool_certificate_templates', ['id' => $this->template->get_id()]);
        $this->assertFalse($condition->is_available(true, $this->info, false, $this->learner->id));
    }

    public function test_same_site_copy_uses_destination_course_and_foreign_restore_closes(): void {
        $this->issue();
        $course = $this->getDataGenerator()->create_course();
        $page = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
        $info = new \core_availability\info_module(get_fast_modinfo($course)->get_cm($page->cmid));
        $logger = new \output_text_logger(\backup::LOG_NONE);
        $condition = new condition($this->settings());
        $this->assertTrue($condition->update_after_restore('test', $course->id, $logger, 'Page'));
        $this->assertTrue($condition->is_available(false, $info, false, $this->learner->id));
        $foreign = new condition($this->settings(['site' => str_repeat('b', 64)]));
        $this->assertTrue($foreign->update_after_restore('test', $course->id, $logger, 'Page'));
        $this->assertSame(0, $foreign->save()->templateid);
        $this->assertFalse($foreign->is_available(false, $info, false, $this->learner->id));
    }

    public function test_learner_description_hides_template_name_and_editor_options_are_permission_scoped(): void {
        $condition = new condition($this->settings());
        $this->setUser($this->learner);
        $this->assertStringNotContainsString('Old award', $condition->get_description(false, false, $this->info));
        $frontend = new frontend();
        $method = new \ReflectionMethod($frontend, 'get_javascript_init_params');
        $method->setAccessible(true);
        $data = $method->invoke($frontend, $this->course);
        $this->assertSame([], $data[0]);
        $this->assertSame([], $data[1]);
        $this->setAdminUser();
        $this->assertStringContainsString('Old award', $condition->get_description(true, false, $this->info));
    }

    public function test_descriptions_state_the_result_for_both_polarities_and_filters(): void {
        global $CFG;
        // Native test resets remove dataroot language packs. Keep metadata in the source fixture;
        // the actual translated condition strings are loaded from this plugin's lang/de directory.
        $langroot = $CFG->langotherroot;
        $CFG->langotherroot = __DIR__ . '/fixtures/lang';
        get_string_manager(true)->reset_caches();
        $languages = [
            'en' => [
                'positive' => 'A certificate from Old award has been issued',
                'negative' => 'No certificate from Old award has been issued',
                'scope' => ['course' => 'In this course', 'any' => 'Across all courses and issuing methods'],
                'validity' => ['all' => null, 'valid' => 'Not expired', 'expired' => 'Expired'],
                'archive' => ['all' => null, 'current' => 'Not archived', 'archived' => 'Archived'],
            ],
            'de' => [
                'positive' => 'Ein Zertifikat aus Old award wurde ausgestellt',
                'negative' => 'Kein Zertifikat aus Old award wurde ausgestellt',
                'scope' => ['course' => 'In diesem Kurs', 'any' => 'Kursübergreifend und unabhängig vom Ausstellungsweg'],
                'validity' => ['all' => null, 'valid' => 'Nicht abgelaufen', 'expired' => 'Abgelaufen'],
                'archive' => ['all' => null, 'current' => 'Nicht archiviert', 'archived' => 'Archiviert'],
            ],
        ];
        // Explicit truth table: required result, parent NOT, displayed result.
        $cases = [[false, false, 'negative'], [false, true, 'positive'],
            [true, false, 'positive'], [true, true, 'negative']];
        try {
            foreach ($languages as $lang => $words) {
                force_current_language($lang);
                foreach ($words['scope'] as $scope => $scopeword) {
                    foreach ($words['validity'] as $validity => $validityword) {
                        foreach ($words['archive'] as $archive => $archiveword) {
                            $details = array_filter([$scopeword, $validityword, $archiveword]);
                            foreach ($cases as [$received, $not, $result]) {
                                $condition = new condition($this->settings(compact('received', 'scope', 'validity', 'archive')));
                                $expected = $words[$result] . ' (' . implode('; ', $details) . ').';
                                $this->assertSame($expected, $condition->get_description(true, $not, $this->info));
                            }
                        }
                    }
                }
            }
        } finally {
            force_current_language(null);
            $CFG->langotherroot = $langroot;
            get_string_manager(true)->reset_caches();
        }
    }

    public function test_native_combined_descriptions_apply_and_or_and_negation(): void {
        global $OUTPUT;
        force_current_language('en');
        try {
            foreach (['&' => false, '|' => false, '!&' => true, '!|' => true] as $op => $negated) {
                foreach ([false, true] as $received) {
                    $settings = $this->settings(['received' => $received]);
                    $structure = (object)['op' => $op, 'c' => [$settings,
                        (object)['type' => 'date', 'd' => '>=', 't' => 1700000000]]];
                    if (in_array($op, ['&', '!|'], true)) {
                        $structure->showc = [true, true];
                    } else {
                        $structure->show = true;
                    }
                    $tree = new \core_availability\tree($structure);
                    $renderable = new \core_availability\output\availability_info($tree->get_full_information($this->info));
                    $html = $OUTPUT->render($renderable);
                    $positive = ($received && !$negated) || (!$received && $negated);
                    $sentence = ($positive ? 'A certificate' : 'No certificate') .
                        ' from Old award has been issued (In this course).';
                    $this->assertStringContainsString($sentence, $html);
                    $this->assertStringNotContainsString('may have been', $html);
                    $this->assertStringNotContainsString('Any expiry status', $html);
                }
            }
        } finally {
            force_current_language(null);
        }
    }

    public function test_forged_template_scope_or_course_binding_remains_closed(): void {
        foreach (['templateid' => 987654321, 'scope' => 'any',
                'boundcourse' => 987654321, 'binding' => str_repeat('d', 64)] as $field => $value) {
            $settings = $this->settings();
            $settings->{$field} = $value;
            $condition = new condition($settings);
            foreach ([false, true] as $not) {
                $this->assertFalse($condition->is_available($not, $this->info, false, $this->learner->id));
            }
        }
    }

    public function test_automatic_issuance_excludes_old_recipients_and_preserves_history(): void {
        global $DB;
        $other = $this->getDataGenerator()->create_and_enrol($this->course, 'student');
        $oldid = $this->issue(['archived' => 1, 'expires' => 1]);
        $oldbefore = $DB->get_record('tool_certificate_issues', ['id' => $oldid]);
        $completion = new \completion_completion(['course' => $this->course->id, 'userid' => $this->learner->id]);
        $completion->mark_complete(1700000000);
        $before = $DB->get_record('course_completions', ['course' => $this->course->id, 'userid' => $this->learner->id]);
        $newtemplate = $this->getDataGenerator()->get_plugin_generator('tool_certificate')->create_template(
            (object)['name' => 'Replacement award']);
        $tree = ['op' => '&', 'c' => [$this->settings()], 'showc' => [true]];
        $module = $this->getDataGenerator()->create_module('coursecertificate', [
            'course' => $this->course->id, 'template' => $newtemplate->get_id(),
            'availability' => json_encode($tree), 'automaticsend' => 1,
        ]);
        $cm = get_fast_modinfo($this->course)->get_cm($module->cmid);
        $recipients = \mod_coursecertificate\helper::get_users_to_issue($module, $cm);
        $this->assertEquals([$other->id], array_column($recipients, 'id'));
        ob_start();
        try {
            (new \mod_coursecertificate\task\issue_certificates_task())->execute();
        } finally {
            ob_end_clean();
        }
        $this->assertFalse($DB->record_exists('tool_certificate_issues',
            ['templateid' => $newtemplate->get_id(), 'userid' => $this->learner->id]));
        $this->assertTrue($DB->record_exists('tool_certificate_issues',
            ['templateid' => $newtemplate->get_id(), 'userid' => $other->id]));
        $this->assertEquals($oldbefore, $DB->get_record('tool_certificate_issues', ['id' => $oldid]));
        $this->assertEquals($before, $DB->get_record('course_completions',
            ['course' => $this->course->id, 'userid' => $this->learner->id]));
    }
}
