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
 * Certificate Issued language strings.
 *
 * @package    availability_certificateissued
 * @copyright  2026 Andreas Giesen <andreas@108design.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Restriction by issued certificate';
$string['title'] = 'Issued certificate';
$string['description'] = 'Restrict access according to certificates already issued from a selected template.';
$string['template'] = 'Certificate template';
$string['scope'] = 'Where to look';
$string['scope_course'] = 'In this course';
$string['scope_any'] = 'Across all courses and issuing methods';
$string['requirement'] = 'Required result';
$string['received'] = 'A matching certificate has been issued';
$string['notreceived'] = 'No matching certificate has been issued';
$string['validity'] = 'Expiry status';
$string['validity_all'] = 'Any expiry status';
$string['validity_valid'] = 'Not expired';
$string['validity_expired'] = 'Expired';
$string['archive'] = 'Archive status';
$string['archive_all'] = 'Any archive status';
$string['archive_current'] = 'Not archived';
$string['archive_archived'] = 'Archived';
$string['activitypicker'] = 'Copy template from activity';
$string['activityhint'] = 'The activity picker selects its template in this course. Activities using the same template in the same course share the same issue history.';
$string['missing'] = 'Select an available certificate template. Reselect the template after importing from another site.';
$string['invalidselection'] = 'This template is unavailable for selection. Select an available template.';
$string['invalidreference'] = 'This certificate restriction needs a valid template selection by a course editor.';
$string['selectedcertificate'] = 'the selected template';
$string['description_received'] = 'A certificate from {$a->template} has been issued{$a->details}.';
$string['description_notreceived'] = 'No certificate from {$a->template} has been issued{$a->details}.';
$string['editorhelp'] = 'Help for the “Issued certificate” restriction';
$string['helpcontext'] = 'This help explains the “Issued certificate” restriction under “Restrict access”.';
$string['helptopics'] = 'Help topics';
$string['helpmore'] = 'More in the help panel';
$string['scope_help'] = 'in this course counts issues whose recorded course is this course. Across all courses and issuing methods also counts issues from other courses and issues without a course, including manual awards.';
$string['status'] = 'Certificate states';
$string['status_help'] = 'Expiry and archive status are separate filters. Both must match the same issue. Not expired includes certificates without an expiry date. Any status counts historical issues too. Revoked or deleted issues no longer exist and cannot be counted.';
$string['activity'] = 'Copy template from activity';
$string['activity_help'] = 'Selecting an activity copies its template and selects in this course. The saved rule keeps this template selection. Certificate issues do not record the issuing activity: several activities using the same template in one course cannot be distinguished.';
$string['detail_selection'] = 'Template selection and existing issues';
$string['detail_selection_help'] = 'This restriction checks issues from the selected certificate template. The activity picker copies a Course certificate activity’s template. Activities sharing a template in the same course count together.';
$string['topic_selection'] = 'Templates and the activity picker';
$string['topic_selection_body'] = '<p>The <em>Issued certificate</em> restriction checks existing certificate issues for a learner. Select a certificate template that you are permitted to use. Choose <em>in this course</em> to count its issues in the current course, or <em>Across all courses and issuing methods</em> to include other courses and manual awards without a course.</p><p><em>Copy template from activity</em> selects the template used by a Course certificate activity and sets the scope to this course. The saved rule retains the template; later changes to the activity do not change it. Several activities using the same template in one course share their issue history.</p><p>The condition reads existing issues. Completion records, certificate files and issued certificates are preserved.</p>';
$string['topic_states'] = 'Expiry and archive status';
$string['topic_states_body'] = '<p>Choose expiry and archive status independently. Both filters must match a single issue. For example, <em>expired</em> with <em>Archived</em> counts only issues that are both expired and archived.</p><p><em>Not expired</em> includes issues without an expiry date. Archived issues can still be unexpired. <em>No matching certificate has been issued</em> means that none of the user’s issues matches all selected filters.</p><p>Revocation or deletion removes the native issue record. Such issues cannot block access. A state filter can change access when a certificate expires or is archived; use both <em>any</em> options for a permanent exclusion based on retained issue history.</p>';
$string['topic_cutover'] = 'Replacing a certificate';
$string['topic_cutover_body'] = '<p>Stop further issuance of the old certificate before taking it out of use. Prepare the new certificate while it is hidden and retain its usual completion prerequisites.</p><p>In the new certificate activity, add the <em>Issued certificate</em> restriction with these settings: old template, <em>in this course</em>, <em>No matching certificate has been issued</em>, <em>Any expiry status</em>, <em>Any archive status</em>. Combine it with the completion prerequisites using AND.</p><p>Check the result with a learner who has the old certificate and one who has not received it before enabling automatic sending. Existing certificates and course completions remain in place.</p>';
$string['topic_restore'] = 'Course copies and missing templates';
$string['topic_restore_body'] = '<p>Within the same Moodle site, the template selection is retained. <em>in this course</em> refers to the destination course after a copy or restore.</p><p>When restoring on another Moodle site, select the template again. Template numbers can identify different templates on different sites. Until the selection is repaired, this condition denies access, including when it is negated.</p><p>A deleted template also requires a new selection. An activity used by the picker may be removed without changing the saved template rule.</p>';
$string['privacy:metadata'] = 'The plugin stores restriction settings only. It reads existing certificate issues managed by tool_certificate and stores no additional personal data.';
$string['detail_states'] = 'Status filters can change access';
$string['detail_states_help'] = 'Expiry and archive filters can grant access again when an older award changes state. To exclude all retained earlier awards, select Any expiry status and Any archive status.';
$string['updateguard_heading'] = 'Update stopped';
$string['updateguard_component'] = 'The package for {$a} was rejected.';
$string['updateguard_hint'] = 'Use an approved package for the installed edition. You can review the operation again.';
$string['updateguard_unchanged'] = 'This request did not replace plugin files.';
$string['updateguard_help'] = 'The installed plugin checks the publisher signature, package contents, permitted update channel and compatibility before replacement. A valid signature does not authorise a different edition. If one package is rejected, the whole batch stops.';
$string['updateguard_signature'] = 'The publisher signature or package contents could not be verified.';
$string['updateguard_policy'] = 'This package does not match the installed edition or permitted package profile.';
$string['updateguard_channel'] = 'Updates through this channel are not enabled for this plugin.';
$string['updateguard_metadata'] = 'The version or dependency information is inconsistent.';
$string['updateguard_dependency'] = 'A required plugin version is missing. Update the dependency together with this plugin or beforehand.';
$string['updateguard_approval'] = 'The packages or installed state have changed since the preview. Review the operation again.';
$string['updateguard_busy'] = 'Another installation is running. Try again after it finishes.';
$string['updateguard_installed'] = 'The installed update protection is incomplete. Restore the files of the last approved installed version.';
$string['updateguard_operation'] = 'This combination of installation actions is not supported. Start a single update operation again.';
$string['updateguard_failed'] = 'The update could not be verified. No installation was started.';
$string['updateguard_back'] = 'Return to plugin overview';
$string['updateinfo_heading'] = 'Package information: {$a}';
$string['updateinfo_verified'] = 'The embedded publisher record matches the installed release line.';
$string['updateinfo_missing'] = 'This package has no embedded publisher verification record.';
$string['updateinfo_line'] = 'This package identifies a different release line from the installed plugin.';
$string['updateinfo_mismatch'] = 'The embedded publisher record could not be verified against the installed plugin\'s publisher information.';
$string['updateinfo_unavailable'] = 'Publisher information for the selected package could not be read.';
$string['updateinfo_choice'] = 'Review the package source and Moodle\'s validation details before continuing. Use "{$a}" below to cancel.';
