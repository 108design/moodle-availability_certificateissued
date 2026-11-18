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
 * Server-authenticated template selections for native availability JSON.
 * @package    availability_certificateissued
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_certificateissued\local;

defined('MOODLE_INTERNAL') || die();

/** Prevent forged JSON from selecting templates outside the editor's native permissions. */
final class selection {
    /**
     * Create the private configuration key only in an authorised editing context.
     * @param \context $context Course being edited
     */
    public static function prepare(\context $context): void {
        require_capability('moodle/course:manageactivities', $context);
        if (!get_config('availability_certificateissued', 'selectionkey')) {
            // A concurrent first editor may need to reload; a superseded signature denies access.
            set_config('selectionkey', bin2hex(random_bytes(32)), 'availability_certificateissued');
        }
    }

    /**
     * Called only after native template permissions have supplied a permitted selection.
     * No externally callable API exposes this method or the private key.
     * @param int $templateid Selected template
     * @param int $courseid Owning course
     * @param string $scope Selected search scope
     * @return string Authentication tag, or empty when configuration is missing
     */
    public static function signature(int $templateid, int $courseid, string $scope): string {
        $key = get_config('availability_certificateissued', 'selectionkey');
        if (!$key) {
            return '';
        }
        return hash_hmac('sha256', implode('|', [
            'certificateissued', \availability_certificateissued\condition::site_key(), $templateid, $courseid, $scope,
        ]), $key);
    }
}
