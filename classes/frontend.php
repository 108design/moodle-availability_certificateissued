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
 * Native restriction editor integration.
 * @package    availability_certificateissued
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_certificateissued;

defined('MOODLE_INTERNAL') || die();

/** Offer native-permitted templates and same-course activity selection helpers. */
class frontend extends \core_availability\frontend {
    /** @return array Editor language identifiers. */
    protected function get_javascript_strings() {
        return ['template', 'scope', 'scope_course', 'scope_any', 'received', 'notreceived', 'requirement',
            'validity', 'validity_all', 'validity_valid', 'validity_expired', 'archive', 'archive_all',
            'archive_current', 'archive_archived', 'activitypicker', 'activityhint', 'missing',
            'invalidselection', 'editorhelp', 'activity_help', 'status_help', 'scope_help'];
    }

    /**
     * @param \stdClass $course Course being edited
     * @param \cm_info|null $cm Activity being edited
     * @param \section_info|null $section Section being edited
     * @return array Initial data, with no user/issue information
     */
    protected function get_javascript_init_params($course, ?\cm_info $cm = null, ?\section_info $section = null) {
        global $DB, $PAGE, $OUTPUT;
        $context = \context_course::instance($course->id);
        $templates = [];
        $activities = [];
        if (has_capability('moodle/course:manageactivities', $context)) {
            \availability_certificateissued\local\selection::prepare($context);
            $visible = \tool_certificate\permission::get_visible_templates($context);
            foreach ($visible as $template) {
                $templates[] = ['id' => (int)$template->id, 'bindings' => [
                    'course' => \availability_certificateissued\local\selection::signature(
                        (int)$template->id, (int)$course->id, 'course'),
                    'any' => \availability_certificateissued\local\selection::signature(
                        (int)$template->id, (int)$course->id, 'any'),
                ],
                    'name' => html_to_text(format_string($template->name, true, ['context' => $context]), 0, false)];
            }
            // Reading the module table avoids recursively evaluating native availability.
            if (\core_component::get_plugin_directory('mod', 'coursecertificate')) {
                $records = $DB->get_records_sql('SELECT cm.id, cc.name, cc.template
                      FROM {course_modules} cm
                      JOIN {modules} m ON m.id = cm.module
                      JOIN {coursecertificate} cc ON cc.id = cm.instance
                     WHERE cm.course = :courseid AND m.name = :modname AND cm.deletioninprogress = 0
                     ORDER BY cc.name, cm.id', ['courseid' => $course->id, 'modname' => 'coursecertificate']);
                foreach ($records as $record) {
                    if ((!$cm || $cm->id != $record->id) && isset($visible[$record->template])) {
                        $activities[] = ['id' => (int)$record->id, 'templateid' => (int)$record->template,
                            'name' => html_to_text(format_string($record->name, true, ['context' => $context]), 0, false)];
                    }
                }
            }
            \availability_certificateissued\local\help::prepare();
        }
        return [$templates, $activities, condition::site_key(), (int)$course->id, [
            'scope' => $OUTPUT->help_icon('scope', 'availability_certificateissued'),
            'status' => $OUTPUT->help_icon('status', 'availability_certificateissued'),
            'activity' => $OUTPUT->help_icon('activity', 'availability_certificateissued'),
            'details' => \availability_certificateissued\local\help::icon('selection'),
            'states' => \availability_certificateissued\local\help::icon('states'),
        ]];
    }

    /**
     * @param \stdClass $course Course being edited
     * @param \cm_info|null $cm Activity being edited
     * @param \section_info|null $section Section being edited
     * @return bool Whether the editor can offer any permitted template
     */
    protected function allow_add($course, ?\cm_info $cm = null, ?\section_info $section = null) {
        return !empty(\tool_certificate\permission::get_visible_templates(\context_course::instance($course->id)));
    }
}
