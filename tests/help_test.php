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
 * Native page lifecycle regression tests for contextual help.
 * @package    availability_certificateissued
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_certificateissued;

defined('MOODLE_INTERNAL') || die();

final class help_test extends \advanced_testcase {
    protected function setUp(): void {
        global $CFG, $PAGE;
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enableavailability = true;
        $course = $this->getDataGenerator()->create_course();
        $PAGE->set_course($course);
        $PAGE->set_context(\context_course::instance($course->id));
        $this->reset_prepared();
    }

    protected function tearDown(): void {
        $this->reset_prepared();
        parent::tearDown();
    }

    /** Reset request-local state when PHPUnit reuses the PHP process. */
    private function reset_prepared(): void {
        $property = new \ReflectionProperty(\availability_certificateissued\local\help::class, 'prepared');
        $property->setAccessible(true);
        $property->setValue(null, false);
    }

    public function test_section_help_is_prepared_before_header_and_reusable_after_header(): void {
        global $PAGE;
        $PAGE->set_url('/course/editsection.php', ['id' => 123]);
        \availability_certificateissued\local\help::before_headers(new \core\hook\output\before_http_headers($PAGE->get_renderer('core')));
        $this->assertStringContainsString('availability-certificateissued-admin-help-page', $PAGE->bodyclasses);
        $this->assertStringContainsString('data-certissued-help-open',
            \availability_certificateissued\local\help::navbar($PAGE->get_renderer('core')));
        $PAGE->set_state(\moodle_page::STATE_PRINTING_HEADER);
        \availability_certificateissued\local\help::prepare();
        $this->assertStringContainsString('data-certissued-help-open',
            \availability_certificateissued\local\help::navbar($PAGE->get_renderer('core')));
    }

    public function test_activity_editor_uses_the_same_early_hook(): void {
        global $PAGE;
        $PAGE->set_url('/course/modedit.php', ['update' => 123]);
        \availability_certificateissued\local\help::before_headers(new \core\hook\output\before_http_headers($PAGE->get_renderer('core')));
        $this->assertStringContainsString('availability-certificateissued-admin-help-page', $PAGE->bodyclasses);
        $this->assertNotEmpty(\availability_certificateissued\local\help::navbar($PAGE->get_renderer('core')));
    }

    public function test_unprepared_late_call_does_not_mutate_started_output(): void {
        global $PAGE;
        $PAGE->set_url('/course/editsection.php', ['id' => 123]);
        $PAGE->set_state(\moodle_page::STATE_PRINTING_HEADER);
        \availability_certificateissued\local\help::prepare();
        $this->assertStringNotContainsString('availability-certificateissued-admin-help-page', $PAGE->bodyclasses);
        $this->assertSame('', \availability_certificateissued\local\help::navbar($PAGE->get_renderer('core')));
    }

    public function test_unrelated_page_does_not_receive_editor_help(): void {
        global $PAGE;
        $PAGE->set_url('/course/view.php', ['id' => $PAGE->course->id]);
        \availability_certificateissued\local\help::before_headers(new \core\hook\output\before_http_headers($PAGE->get_renderer('core')));
        $this->assertStringNotContainsString('availability-certificateissued-admin-help-page', $PAGE->bodyclasses);
        $this->assertSame('', \availability_certificateissued\local\help::navbar($PAGE->get_renderer('core')));
    }

    public function test_disabled_availability_does_not_prepare_editor_help(): void {
        global $CFG, $PAGE;
        $PAGE->set_url('/course/editsection.php', ['id' => 123]);
        $CFG->enableavailability = false;
        \availability_certificateissued\local\help::before_headers(new \core\hook\output\before_http_headers($PAGE->get_renderer('core')));
        $this->assertSame('', \availability_certificateissued\local\help::navbar($PAGE->get_renderer('core')));
    }

    public function test_editor_help_requires_native_activity_management_permission(): void {
        global $PAGE;
        $PAGE->set_url('/course/editsection.php', ['id' => 123]);
        $this->setGuestUser();
        \availability_certificateissued\local\help::before_headers(new \core\hook\output\before_http_headers($PAGE->get_renderer('core')));
        $this->assertSame('', \availability_certificateissued\local\help::navbar($PAGE->get_renderer('core')));
    }
}
