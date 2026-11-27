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

$string['missing'] = 'Wähle eine verfügbare Zertifikatsvorlage. Nach einem Import von einer anderen Moodle-Website musst du sie erneut auswählen.';
$string['invalidselection'] = 'Diese Vorlage steht zur Auswahl nicht zur Verfügung. Wähle eine verfügbare Vorlage.';
$string['topic_selection_body'] = '<p>Die Voraussetzung <em>Zertifikat ausgestellt</em> prüft, ob für eine Person bereits ein Zertifikat aus der gewählten Vorlage ausgestellt wurde. Wähle eine Zertifikatsvorlage, die du verwenden darfst. Mit <em>In diesem Kurs</em> zählen ihre Ausstellungen im aktuellen Kurs. <em>Kursübergreifend und unabhängig vom Ausstellungsweg</em> berücksichtigt auch andere Kurse und manuelle Ausstellungen ohne Kurszuordnung.</p><p><em>Vorlage aus Aktivität übernehmen</em> wählt die Vorlage einer Kurszertifikatsaktivität und den Suchbereich dieses Kurses aus. Die gespeicherte Regel behält diese Vorlage; spätere Änderungen der Aktivität verändern sie nicht. Mehrere Aktivitäten mit derselben Vorlage im selben Kurs teilen sich die Ausstellungshistorie.</p><p>Die Voraussetzung liest vorhandene Ausstellungen. Abschlussdaten, Zertifikatsdateien und ausgestellte Zertifikate bleiben erhalten.</p>';
$string['topic_states_body'] = '<p>Wähle Ablaufstatus und Archivstatus unabhängig voneinander. Beide Filter müssen auf dieselbe Ausstellung zutreffen. <em>Abgelaufen</em> zusammen mit <em>Archiviert</em> berücksichtigt beispielsweise nur Ausstellungen, die abgelaufen und archiviert sind.</p><p><em>Nicht abgelaufen</em> schließt Zertifikate ohne Ablaufdatum ein. Archivierte Zertifikate können weiterhin nicht abgelaufen sein. <em>Kein passendes Zertifikat wurde ausgestellt</em> bedeutet, dass keine Ausstellung der Person sämtliche gewählten Filter erfüllt.</p><p>Widerruf oder Löschung entfernt die ursprüngliche Ausstellung. Sie kann dann keinen Zugriff mehr sperren. Statusfilter können den Zugriff ändern, wenn ein Zertifikat abläuft oder archiviert wird. Für einen dauerhaften Ausschluss anhand erhaltener Ausstellungen wählst du <em>Jeder Ablaufstatus</em> und <em>Jeder Archivstatus</em>.</p>';
$string['topic_cutover_body'] = '<p>Beende die weitere Ausstellung des alten Zertifikats, bevor du es außer Verwendung nimmst. Bereite das neue Zertifikat zunächst verborgen vor und behalte seine fachlichen Abschlussvoraussetzungen bei.</p><p>Ergänze beim neuen Zertifikat die Voraussetzung <em>Zertifikat ausgestellt</em> mit folgenden Einstellungen: alte Vorlage, <em>In diesem Kurs</em>, <em>Kein passendes Zertifikat wurde ausgestellt</em>, <em>Jeder Ablaufstatus</em>, <em>Jeder Archivstatus</em>. Verknüpfe sie mit den Abschlussvoraussetzungen über UND.</p><p>Prüfe das Ergebnis mit einer Person mit altem Zertifikat und einer Person ohne altes Zertifikat, bevor du den automatischen Versand aktivierst. Vorhandene Zertifikate und Kursabschlüsse bleiben erhalten.</p>';
$string['topic_restore_body'] = '<p>Innerhalb derselben Moodle-Website bleibt die Vorlagenauswahl erhalten. <em>In diesem Kurs</em> bezieht sich nach einer Kurskopie oder Wiederherstellung auf den Zielkurs.</p><p>Nach einer Wiederherstellung auf einer anderen Moodle-Website musst du die Vorlage erneut auswählen. Gleiche Vorlagennummern können dort andere Vorlagen bezeichnen. Bis die Auswahl korrigiert ist, sperrt diese Bedingung den Zugriff, auch bei einer Verneinung.</p><p>Auch eine gelöschte Vorlage erfordert eine neue Auswahl. Eine Aktivität, aus der du die Vorlage übernommen hast, kann entfernt werden, ohne die gespeicherte Vorlagenregel zu verändern.</p>';
$string['detail_states_help'] = 'Ablauf- und Archivfilter können den Zugriff erneut freigeben, wenn eine frühere Ausstellung ihren Status ändert. Für den Ausschluss aller erhaltenen früheren Ausstellungen wählst du Jeder Ablaufstatus und Jeder Archivstatus.';
$string['updateguard_hint'] = 'Verwende ein freigegebenes Paket für die installierte Ausgabe. Du kannst den Vorgang erneut prüfen.';
$string['updateguard_dependency'] = 'Eine benötigte Plugin-Version fehlt. Aktualisiere die Abhängigkeit gemeinsam mit diesem Plugin oder vorher.';
$string['updateguard_approval'] = 'Die Pakete oder der installierte Stand haben sich seit der Vorschau geändert. Prüfe den Vorgang erneut.';
$string['updateguard_busy'] = 'Eine andere Installation läuft. Versuche es nach deren Abschluss erneut.';
$string['updateguard_installed'] = 'Der installierte Updateschutz ist unvollständig. Stelle die Dateien der zuletzt installierten freigegebenen Version wieder her.';
$string['updateguard_operation'] = 'Diese Kombination von Installationsaktionen wird nicht unterstützt. Starte einen einzelnen Updatevorgang erneut.';
$string['updateinfo_choice'] = 'Kontrolliere vor dem Fortfahren die Paketquelle und Moodles Prüfergebnisse. Mit „{$a}“ unten kannst du abbrechen.';
