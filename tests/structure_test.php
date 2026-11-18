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
 * Configuration validation without database-reset fixtures.
 * @package    availability_certificateissued
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_certificateissued;

defined('MOODLE_INTERNAL') || die();

/** Pure JSON validation and serialization checks. */
final class structure_test extends \basic_testcase {
    /** @return \stdClass Synthetic selection without a real template lookup. */
    private function settings(): \stdClass {
        return (object)[
            'templateid' => 3, 'site' => str_repeat('c', 64), 'received' => false,
            'scope' => 'course', 'validity' => 'all', 'archive' => 'all',
            'boundcourse' => 5, 'binding' => str_repeat('e', 64),
        ];
    }

    public function test_serialisation_does_not_share_mutable_settings(): void {
        $settings = $this->settings();
        $condition = new condition($settings);
        $settings->templateid = 0;
        $this->assertSame(3, $condition->save()->templateid);
        $saved = $condition->save();
        $saved->templateid = 0;
        $this->assertSame(3, $condition->save()->templateid);
    }

    public function test_rejects_unknown_state_values_and_wrong_types(): void {
        foreach (['validity' => 'unexpected', 'archive' => 'unexpected', 'scope' => 'unexpected',
                'templateid' => '3', 'received' => 0, 'site' => 'different-site'] as $field => $value) {
            $settings = $this->settings();
            $settings->{$field} = $value;
            try {
                new condition($settings);
                $this->fail('Accepted invalid ' . $field);
            } catch (\coding_exception $e) {
                $this->assertStringContainsString('Invalid Certificate Issued', $e->getMessage());
            }
        }
    }

    public function test_missing_selection_can_be_serialised_as_closed(): void {
        $settings = $this->settings();
        $settings->templateid = 0;
        $this->assertSame(0, (new condition($settings))->save()->templateid);
    }
}
