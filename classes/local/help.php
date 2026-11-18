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
 * Editor help, scoped to authorised course-edit forms.
 * @package    availability_certificateissued
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace availability_certificateissued\local;

defined('MOODLE_INTERNAL') || die();

/** Standalone topic panel with native help popovers and Topnav access. */
final class help {
    /** @var bool Whether this request is an authorised editor. */
    private static $prepared = false;
    /** @var array Topics available to course editors. */
    private const TOPICS = ['selection', 'states', 'cutover', 'restore'];

    /** Register page assets while body classes and CSS can still be changed. */
    public static function before_headers(\core\hook\output\before_http_headers $hook): void {
        global $CFG, $PAGE;
        if (empty($CFG->enableavailability)) {
            return;
        }
        foreach (['/course/modedit.php', '/course/editsection.php'] as $path) {
            if ($PAGE->url->compare(new \moodle_url($path), URL_MATCH_BASE)) {
                self::prepare();
                return;
            }
        }
    }

    /** Register assets before the native form header is emitted. */
    public static function prepare(): void {
        global $PAGE;
        if (self::$prepared || $PAGE->state !== \moodle_page::STATE_BEFORE_HEADER || !$PAGE->context ||
                !has_capability('moodle/course:manageactivities', $PAGE->context)) {
            return;
        }
        self::$prepared = true;
        $PAGE->add_body_class('availability-certificateissued-admin-help-page');
        $PAGE->requires->css(new \moodle_url('/availability/condition/certificateissued/styles.css',
            ['rev' => get_config('availability_certificateissued', 'version')]));
        $PAGE->requires->js_call_amd('availability_certificateissued/help', 'init', [[
            'topic' => optional_param('certissuedhelp', '', PARAM_ALPHA),
        ]]);
    }

    /**
     * @param string $topic Allowed topic
     * @return string Native information popover linking its exact topic
     */
    public static function icon(string $topic): string {
        global $CFG, $PAGE, $OUTPUT;
        if (!self::$prepared || !in_array($topic, self::TOPICS, true)) {
            return '';
        }
        $url = new \moodle_url($PAGE->url, ['certissuedhelp' => $topic]);
        $icon = new \help_icon('detail_' . $topic, 'availability_certificateissued');
        $data = (array)$icon->export_for_template($OUTPUT);
        $data['completedoclink'] = \html_writer::link($url,
            get_string('helpmore', 'availability_certificateissued'),
            ['class' => 'availability-certificateissued-topic-link']);
        $data['legacy'] = $CFG->version < 2025041400;
        $data['infoicon'] = $CFG->version >= 2026100500 ? 'i/circleinfo' : 'i/info';
        return $OUTPUT->render_from_template('availability_certificateissued/help_icon', $data);
    }

    /**
     * @param \renderer_base $renderer Native renderer
     * @return string Topnav entry
     */
    public static function navbar(\renderer_base $renderer): string {
        global $CFG;
        if (!self::$prepared) {
            return '';
        }
        $title = get_string('editorhelp', 'availability_certificateissued');
        return \html_writer::tag('button', $renderer->pix_icon(
            $CFG->version >= 2026100500 ? 'i/circleinfo' : 'i/info', ''), [
                'type' => 'button', 'class' => 'nav-link icon-no-margin availability-certificateissued-help-navbar',
                'data-certissued-help-open' => '', 'title' => $title, 'aria-label' => $title,
                'hidden' => 'hidden',
                'aria-controls' => 'availability-certificateissued-help', 'aria-expanded' => 'false',
            ]);
    }

    /**
     * @param \core\hook\output\before_footer_html_generation $hook Native footer output
     */
    public static function footer(\core\hook\output\before_footer_html_generation $hook): void {
        $hook->add_html(self::render());
    }

    /** @return string Authorised editor-only panel. */
    public static function render(): string {
        global $OUTPUT;
        if (!self::$prepared) {
            return '';
        }
        $options = [];
        $articles = '';
        foreach (self::TOPICS as $topic) {
            $title = get_string('topic_' . $topic, 'availability_certificateissued');
            $options[$topic] = $title;
            $articles .= \html_writer::tag('article',
                \html_writer::tag('h3', $title, ['tabindex' => '-1', 'class' => 'h5']) .
                get_string('topic_' . $topic . '_body', 'availability_certificateissued'),
                ['data-certissued-help-topic' => $topic, 'hidden' => 'hidden']);
        }
        $choice = \html_writer::tag('label', get_string('helptopics', 'availability_certificateissued'),
            ['for' => 'certissued-help-topic']) .
            \html_writer::select($options, 'certissued-help-topic', 'selection', false,
                ['id' => 'certissued-help-topic', 'class' => 'form-select custom-select']);
        $brand = \html_writer::span(\html_writer::empty_tag('img', [
            'src' => $OUTPUT->image_url('adminbrand', 'availability_certificateissued'),
            'alt' => '', 'width' => 25, 'height' => 24]), 'availability-certificateissued-help-brand',
            ['role' => 'img', 'aria-label' => get_string('title', 'availability_certificateissued')]);
        $close = \html_writer::tag('button', '&#215;', ['type' => 'button',
            'class' => 'btn btn-secondary btn-sm availability-certificateissued-help-close',
            'data-certissued-help-close' => '', 'aria-label' => get_string('closebuttontitle', 'moodle')]);
        $header = \html_writer::tag('header', \html_writer::div($choice,
            'availability-certificateissued-help-choices') . $brand . $close,
            ['class' => 'availability-certificateissued-help-header availability-certificateissued-help-header-branded']);
        $context = \html_writer::tag('p', get_string('helpcontext', 'availability_certificateissued'),
            ['class' => 'small text-muted']);
        return \html_writer::tag('aside', $header . \html_writer::div($context . $articles,
            'availability-certificateissued-help-content'), ['id' => 'availability-certificateissued-help',
                'class' => 'availability-certificateissued-help', 'hidden' => 'hidden',
                'aria-label' => get_string('editorhelp', 'availability_certificateissued')]);
    }
}
