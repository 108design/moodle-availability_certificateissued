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
 * Native output hook available in Moodle4.5 and later.
 * @package    availability_certificateissued
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\output\before_standard_head_html_generation::class,
        'callback' => [\availability_certificateissued\local\admin_branding::class, 'head'],
    ],
    [
        'hook' => \core\hook\output\before_http_headers::class,
        'callback' => [\availability_certificateissued\local\help::class, 'before_headers'],
    ],
    [
        'hook' => \core\hook\output\before_footer_html_generation::class,
        'callback' => [\availability_certificateissued\local\help::class, 'footer'],
    ],
];

// Native installation and updates are handled by Moodle Core.

// Optional package information; Core owns preview, confirmation and installation.
$callbacks[] = [
    'hook' => \core\hook\after_config::class,
    'callback' => [\availability_certificateissued\local\update_notice::class, 'after_config'],
    'priority' => 1003,
];
$callbacks[] = [
    'hook' => \core\hook\output\before_standard_top_of_body_html_generation::class,
    'callback' => [\availability_certificateissued\local\update_notice::class, 'before_body'],
];
