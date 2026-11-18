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
 * Availability based on existing certificate issues.
 * @package    availability_certificateissued
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_certificateissued;

use core_availability\info;

defined('MOODLE_INTERNAL') || die();

/** A template predicate; the activity picker is deliberately not issue provenance. */
class condition extends \core_availability\condition {
    /** @var \stdClass Validated, serialisable settings. */
    private $settings;

    /**
     * Validate persisted settings. Missing references use ID zero and remain closed.
     * @param \stdClass $structure Native availability JSON
     */
    public function __construct($structure) {
        if (!isset($structure->templateid) || !is_int($structure->templateid) || $structure->templateid < 0 ||
                !isset($structure->site) || !is_string($structure->site) ||
                !preg_match('/^[a-f0-9]{64}$/D', $structure->site) ||
                !isset($structure->received) || !is_bool($structure->received) ||
                !isset($structure->boundcourse) || !is_int($structure->boundcourse) || $structure->boundcourse <= 0 ||
                !isset($structure->binding) || !is_string($structure->binding) ||
                !preg_match('/^[a-f0-9]{64}$/D', $structure->binding) ||
                !isset($structure->scope) || !in_array($structure->scope, ['course', 'any'], true) ||
                !isset($structure->validity) || !in_array($structure->validity, ['all', 'valid', 'expired'], true) ||
                !isset($structure->archive) || !in_array($structure->archive, ['all', 'current', 'archived'], true)) {
            throw new \coding_exception('Invalid Certificate Issued condition settings');
        }
        $this->settings = (object)[
            'type' => 'certificateissued',
            'templateid' => $structure->templateid,
            'site' => $structure->site,
            'received' => $structure->received,
            'boundcourse' => $structure->boundcourse,
            'binding' => $structure->binding,
            'scope' => $structure->scope,
            'validity' => $structure->validity,
            'archive' => $structure->archive,
        ];
    }

    /** @return string Non-secret site identity prevents accidental cross-site ID reuse. */
    public static function site_key(): string {
        global $CFG;
        return hash('sha256', $CFG->siteidentifier);
    }

    /** @return \stdClass A detached copy for Moodle's availability serializer. */
    public function save() {
        return clone $this->settings;
    }

    /**
     * Missing template/site bindings must never become access through negation.
     * @return \stdClass|null Native template record
     */
    private function template(): ?\stdClass {
        global $DB;
        $signature = \availability_certificateissued\local\selection::signature(
            $this->settings->templateid, $this->settings->boundcourse, $this->settings->scope);
        if (!$signature || !hash_equals($signature, $this->settings->binding) ||
                !$this->settings->templateid || !hash_equals(self::site_key(), $this->settings->site) ||
                !\core_component::get_plugin_directory('tool', 'certificate')) {
            return null;
        }
        return $DB->get_record('tool_certificate_templates', ['id' => $this->settings->templateid]) ?: null;
    }

    /**
     * Build one portable, parameterised predicate for all consumers.
     * @param info $info Current course
     * @return array SQL WHERE and bound values
     */
    private function predicate(info $info): array {
        $where = 'ci.templateid = :certtemplate';
        $params = ['certtemplate' => $this->settings->templateid];
        if ($this->settings->scope === 'course') {
            $where .= ' AND ci.courseid = :certcourse';
            $params['certcourse'] = $info->get_course()->id;
        }
        if ($this->settings->archive !== 'all') {
            $where .= ' AND ci.archived = :certarchived';
            $params['certarchived'] = (int)($this->settings->archive === 'archived');
        }
        if ($this->settings->validity !== 'all') {
            // Match tool_certificate: expires > 0 AND expires < now means expired.
            $params['certnow'] = \core\di::get(\core\clock::class)->time();
            if ($this->settings->validity === 'expired') {
                $where .= ' AND ci.expires > 0 AND ci.expires < :certnow';
            } else {
                $where .= ' AND (ci.expires IS NULL OR ci.expires <= 0 OR ci.expires >= :certnow)';
            }
        }
        return [$where, $params];
    }

    /**
     * Evaluate only the requested user's native records.
     * @param bool $not Parent NOT
     * @param info $info Availability context
     * @param bool $grabthelot Native performance hint
     * @param int $userid Evaluated user
     * @return bool Access decision
     */
    public function is_available($not, info $info, $grabthelot, $userid) {
        global $DB;
        if (!$this->template() || $userid <= 0 || $this->settings->boundcourse !== (int)$info->get_course()->id) {
            return false;
        }
        [$where, $params] = $this->predicate($info);
        $params['certuser'] = $userid;
        $exists = $DB->record_exists_sql('SELECT 1 FROM {tool_certificate_issues} ci WHERE ' . $where .
            ' AND ci.userid = :certuser', $params);
        return ($exists === $this->settings->received) !== (bool)$not;
    }

    /**
     * Availability changes when an issue is issued, revoked, archived or expires.
     * Native permanent user-list filters must not hide time-dependent recipients.
     * mod_coursecertificate separately checks each recipient's final availability.
     * @return bool
     */
    public function is_applied_to_user_lists() {
        return false;
    }

    /**
     * Human-readable rule, without revealing inaccessible template titles to learners.
     * @param bool $full Full staff explanation
     * @param bool $not Parent NOT
     * @param info $info Availability context
     * @return string
     */
    public function get_description($full, $not, info $info) {
        $template = $this->template();
        if (!$template || $this->settings->boundcourse !== (int)$info->get_course()->id) {
            return get_string('invalidreference', 'availability_certificateissued');
        }
        $context = \context_course::instance($info->get_course()->id);
        $name = get_string('selectedcertificate', 'availability_certificateissued');
        if (has_capability('moodle/course:manageactivities', $context)) {
            $name = format_string($template->name, true, ['context' => $context]);
        }
        $details = [get_string('scope_' . $this->settings->scope, 'availability_certificateissued')];
        if ($this->settings->validity !== 'all') {
            $details[] = get_string('validity_' . $this->settings->validity, 'availability_certificateissued');
        }
        if ($this->settings->archive !== 'all') {
            $details[] = get_string('archive_' . $this->settings->archive, 'availability_certificateissued');
        }
        $a = (object)['template' => $name, 'details' => ' (' . implode('; ', $details) . ')'];
        $positive = $this->settings->received !== (bool)$not;
        return get_string($positive ? 'description_received' : 'description_notreceived',
            'availability_certificateissued', $a);
    }

    /** @return string Diagnostic values only. */
    protected function get_debug_string() {
        return 'template=' . $this->settings->templateid . ',scope=' . $this->settings->scope .
            ',received=' . (int)$this->settings->received . ',validity=' . $this->settings->validity .
            ',archive=' . $this->settings->archive;
    }

    /**
     * System templates have no course-backup mapping. A foreign site needs reselection.
     * @param string $restoreid Native restore identifier
     * @param int $courseid Destination course
     * @param \base_logger $logger Restore logger
     * @param string $name Restored item
     * @return bool Whether settings changed
     */
    public function update_after_restore($restoreid, $courseid, \base_logger $logger, $name) {
        $context = \context_course::instance($courseid);
        $visible = \tool_certificate\permission::get_visible_templates($context);
        if (!$this->template() || !isset($visible[$this->settings->templateid])) {
            $this->settings->templateid = 0;
            $this->settings->site = self::site_key();
            $logger->process('Certificate Issued restriction for ' . $name . ' needs template reselection.', \backup::LOG_WARNING);
            return true;
        }
        if ($this->settings->boundcourse !== (int)$courseid) {
            $this->settings->boundcourse = (int)$courseid;
            $this->settings->binding = \availability_certificateissued\local\selection::signature(
                $this->settings->templateid, (int)$courseid, $this->settings->scope);
            return true;
        }
        return false;
    }
}
