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

$string['pluginname'] = 'Voraussetzung „Zertifikat ausgestellt“';
$string['title'] = 'Zertifikat ausgestellt';
$string['description'] = 'Zugriff anhand bereits ausgestellter Zertifikate einer ausgewählten Vorlage steuern.';
$string['template'] = 'Zertifikatsvorlage';
$string['scope'] = 'Suchbereich';
$string['scope_course'] = 'In diesem Kurs';
$string['scope_any'] = 'Kursübergreifend und unabhängig vom Ausstellungsweg';
$string['requirement'] = 'Erforderliches Ergebnis';
$string['received'] = 'Ein passendes Zertifikat wurde ausgestellt';
$string['notreceived'] = 'Kein passendes Zertifikat wurde ausgestellt';
$string['validity'] = 'Ablaufstatus';
$string['validity_all'] = 'Jeder Ablaufstatus';
$string['validity_valid'] = 'Nicht abgelaufen';
$string['validity_expired'] = 'Abgelaufen';
$string['archive'] = 'Archivstatus';
$string['archive_all'] = 'Jeder Archivstatus';
$string['archive_current'] = 'Nicht archiviert';
$string['archive_archived'] = 'Archiviert';
$string['activitypicker'] = 'Vorlage aus Aktivität übernehmen';
$string['activityhint'] = 'Die Aktivitätsauswahl übernimmt deren Vorlage für diesen Kurs. Aktivitäten mit derselben Vorlage im selben Kurs teilen sich die Ausstellungshistorie.';
$string['missing'] = 'Wählen Sie eine verfügbare Zertifikatsvorlage. Nach einem Import von einer anderen Moodle-Website müssen Sie sie erneut auswählen.';
$string['invalidselection'] = 'Diese Vorlage steht zur Auswahl nicht zur Verfügung. Wählen Sie eine verfügbare Vorlage.';
$string['invalidreference'] = 'Für diese Zertifikatsvoraussetzung muss die Kursverwaltung eine gültige Vorlage auswählen.';
$string['selectedcertificate'] = 'der ausgewählten Zertifikatsvorlage';
$string['description_received'] = 'Ein Zertifikat aus {$a->template} wurde ausgestellt{$a->details}.';
$string['description_notreceived'] = 'Kein Zertifikat aus {$a->template} wurde ausgestellt{$a->details}.';
$string['editorhelp'] = 'Hilfe zur Voraussetzung „Zertifikat ausgestellt“';
$string['helpcontext'] = 'Diese Hilfe beschreibt die Voraussetzung „Zertifikat ausgestellt“ unter „Voraussetzungen“.';
$string['helptopics'] = 'Hilfethemen';
$string['helpmore'] = 'Mehr dazu im Hilfepanel';
$string['scope_help'] = 'In diesem Kurs zählt Ausstellungen, bei denen dieser Kurs hinterlegt ist. Kursübergreifend und unabhängig vom Ausstellungsweg zählt auch Ausstellungen aus anderen Kursen und ohne Kurszuordnung, etwa manuell vergebene Zertifikate.';
$string['status'] = 'Zertifikatsstatus';
$string['status_help'] = 'Ablaufstatus und Archivstatus sind getrennte Filter. Beide müssen auf dieselbe Ausstellung zutreffen. Nicht abgelaufen schließt Zertifikate ohne Ablaufdatum ein. Jeder Status zählt auch frühere Ausstellungen. Widerrufene oder gelöschte Ausstellungen sind nicht mehr vorhanden und können nicht berücksichtigt werden.';
$string['activity'] = 'Vorlage aus Aktivität übernehmen';
$string['activity_help'] = 'Die Auswahl einer Aktivität übernimmt deren Vorlage und stellt den Suchbereich auf In diesem Kurs. Die gespeicherte Regel behält diese Vorlagenauswahl. Ausstellungen speichern keine ausstellende Aktivität: Mehrere Aktivitäten mit derselben Vorlage im selben Kurs lassen sich deshalb nicht unterscheiden.';
$string['detail_selection'] = 'Vorlagenauswahl und vorhandene Ausstellungen';
$string['detail_selection_help'] = 'Die Voraussetzung prüft Ausstellungen der gewählten Zertifikatsvorlage. Die Aktivitätsauswahl übernimmt die Vorlage einer Kurszertifikatsaktivität. Aktivitäten mit derselben Vorlage im selben Kurs werden gemeinsam berücksichtigt.';
$string['topic_selection'] = 'Vorlagen und Aktivitätsauswahl';
$string['topic_selection_body'] = '<p>Die Voraussetzung <em>Zertifikat ausgestellt</em> prüft, ob für eine Person bereits ein Zertifikat aus der gewählten Vorlage ausgestellt wurde. Wählen Sie eine Zertifikatsvorlage, die Sie verwenden dürfen. Mit <em>In diesem Kurs</em> zählen ihre Ausstellungen im aktuellen Kurs. <em>Kursübergreifend und unabhängig vom Ausstellungsweg</em> berücksichtigt auch andere Kurse und manuelle Ausstellungen ohne Kurszuordnung.</p><p><em>Vorlage aus Aktivität übernehmen</em> wählt die Vorlage einer Kurszertifikatsaktivität und den Suchbereich dieses Kurses aus. Die gespeicherte Regel behält diese Vorlage; spätere Änderungen der Aktivität verändern sie nicht. Mehrere Aktivitäten mit derselben Vorlage im selben Kurs teilen sich die Ausstellungshistorie.</p><p>Die Voraussetzung liest vorhandene Ausstellungen. Abschlussdaten, Zertifikatsdateien und ausgestellte Zertifikate bleiben erhalten.</p>';
$string['topic_states'] = 'Ablaufstatus und Archivstatus';
$string['topic_states_body'] = '<p>Wählen Sie Ablaufstatus und Archivstatus unabhängig voneinander. Beide Filter müssen auf dieselbe Ausstellung zutreffen. <em>Abgelaufen</em> zusammen mit <em>Archiviert</em> berücksichtigt beispielsweise nur Ausstellungen, die abgelaufen und archiviert sind.</p><p><em>Nicht abgelaufen</em> schließt Zertifikate ohne Ablaufdatum ein. Archivierte Zertifikate können weiterhin nicht abgelaufen sein. <em>Kein passendes Zertifikat wurde ausgestellt</em> bedeutet, dass keine Ausstellung der Person sämtliche gewählten Filter erfüllt.</p><p>Widerruf oder Löschung entfernt die ursprüngliche Ausstellung. Sie kann dann keinen Zugriff mehr sperren. Statusfilter können den Zugriff ändern, wenn ein Zertifikat abläuft oder archiviert wird. Für einen dauerhaften Ausschluss anhand erhaltener Ausstellungen wählen Sie <em>Jeder Ablaufstatus</em> und <em>Jeder Archivstatus</em>.</p>';
$string['topic_cutover'] = 'Ein Zertifikat ersetzen';
$string['topic_cutover_body'] = '<p>Beenden Sie die weitere Ausstellung des alten Zertifikats, bevor Sie es außer Verwendung nehmen. Bereiten Sie das neue Zertifikat zunächst verborgen vor und behalten Sie seine fachlichen Abschlussvoraussetzungen bei.</p><p>Ergänzen Sie beim neuen Zertifikat die Voraussetzung <em>Zertifikat ausgestellt</em> mit folgenden Einstellungen: alte Vorlage, <em>In diesem Kurs</em>, <em>Kein passendes Zertifikat wurde ausgestellt</em>, <em>Jeder Ablaufstatus</em>, <em>Jeder Archivstatus</em>. Verknüpfe sie mit den Abschlussvoraussetzungen über UND.</p><p>Prüfen Sie das Ergebnis mit einer Person mit altem Zertifikat und einer Person ohne altes Zertifikat, bevor Sie den automatischen Versand aktivieren. Vorhandene Zertifikate und Kursabschlüsse bleiben erhalten.</p>';
$string['topic_restore'] = 'Kurskopien und fehlende Vorlagen';
$string['topic_restore_body'] = '<p>Innerhalb derselben Moodle-Website bleibt die Vorlagenauswahl erhalten. <em>In diesem Kurs</em> bezieht sich nach einer Kurskopie oder Wiederherstellung auf den Zielkurs.</p><p>Nach einer Wiederherstellung auf einer anderen Moodle-Website müssen Sie die Vorlage erneut auswählen. Gleiche Vorlagennummern können dort andere Vorlagen bezeichnen. Bis die Auswahl korrigiert ist, sperrt diese Bedingung den Zugriff, auch bei einer Verneinung.</p><p>Auch eine gelöschte Vorlage erfordert eine neue Auswahl. Eine Aktivität, aus der Sie die Vorlage übernommen haben, kann entfernt werden, ohne die gespeicherte Vorlagenregel zu verändern.</p>';
$string['privacy:metadata'] = 'Das Plugin speichert ausschließlich Einstellungen der Voraussetzung. Es liest vorhandene, von tool_certificate verwaltete Zertifikatsausstellungen und speichert keine zusätzlichen personenbezogenen Daten.';
$string['detail_states'] = 'Statusfilter können den Zugriff ändern';
$string['detail_states_help'] = 'Ablauf- und Archivfilter können den Zugriff erneut freigeben, wenn eine frühere Ausstellung ihren Status ändert. Für den Ausschluss aller erhaltenen früheren Ausstellungen wählen Sie Jeder Ablaufstatus und Jeder Archivstatus.';
$string['updateguard_heading'] = 'Update angehalten';
$string['updateguard_component'] = 'Das Paket für {$a} wurde abgewiesen.';
$string['updateguard_hint'] = 'Verwenden Sie ein freigegebenes Paket für die installierte Ausgabe. Sie können den Vorgang erneut prüfen.';
$string['updateguard_unchanged'] = 'Dieser Vorgang hat keine Plugin-Dateien ersetzt.';
$string['updateguard_help'] = 'Das installierte Plugin prüft vor dem Ersetzen die Herausgebersignatur, den Paketinhalt, den erlaubten Updatekanal und die Kompatibilität. Eine gültige Signatur erlaubt keinen Wechsel auf eine andere Ausgabe. Wird ein Paket abgewiesen, stoppt die gesamte Sammelaktion.';
$string['updateguard_signature'] = 'Die Herausgebersignatur oder der Paketinhalt konnte nicht bestätigt werden.';
$string['updateguard_policy'] = 'Das Paket passt nicht zur installierten Ausgabe oder zum erlaubten Paketprofil.';
$string['updateguard_channel'] = 'Updates über diesen Kanal sind für dieses Plugin nicht freigegeben.';
$string['updateguard_metadata'] = 'Die Versions- oder Abhängigkeitsangaben sind nicht konsistent.';
$string['updateguard_dependency'] = 'Eine benötigte Plugin-Version fehlt. Aktualisieren Sie die Abhängigkeit gemeinsam mit diesem Plugin oder vorher.';
$string['updateguard_approval'] = 'Die Pakete oder der installierte Stand haben sich seit der Vorschau geändert. Prüfen Sie den Vorgang erneut.';
$string['updateguard_busy'] = 'Eine andere Installation läuft. Versuchen Sie es nach deren Abschluss erneut.';
$string['updateguard_installed'] = 'Der installierte Updateschutz ist unvollständig. Stellen Sie die Dateien der zuletzt installierten freigegebenen Version wieder her.';
$string['updateguard_operation'] = 'Diese Kombination von Installationsaktionen wird nicht unterstützt. Starten Sie einen einzelnen Updatevorgang erneut.';
$string['updateguard_failed'] = 'Das Update konnte nicht geprüft werden. Es wurde keine Installation gestartet.';
$string['updateguard_back'] = 'Zur Pluginübersicht';
$string['updateinfo_heading'] = 'Paketinformationen: {$a}';
$string['updateinfo_verified'] = 'Der eingebettete Herausgebernachweis passt zur installierten Ausgabe.';
$string['updateinfo_missing'] = 'Dieses Paket enthält keinen eingebetteten Herausgebernachweis.';
$string['updateinfo_line'] = 'Dieses Paket gehört zu einer anderen Ausgabe als das installierte Plugin.';
$string['updateinfo_mismatch'] = 'Der eingebettete Herausgebernachweis konnte mit den Angaben des installierten Plugins nicht bestätigt werden.';
$string['updateinfo_unavailable'] = 'Die Herausgeberinformationen für das ausgewählte Paket konnten nicht gelesen werden.';
$string['updateinfo_choice'] = 'Kontrollieren Sie vor dem Fortfahren die Paketquelle und Moodles Prüfergebnisse. Mit „{$a}“ unten können Sie abbrechen.';
