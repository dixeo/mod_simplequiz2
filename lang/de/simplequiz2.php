<?php

// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'simplequiz2', language 'de'.
 *
 * @package    mod_simplequiz2
 * @copyright  2024 Dixeo (contact@dixeo.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Generic.
$string['pluginname'] = 'Simple Quiz';
$string['modulename'] = 'Simple Quiz';
$string['plugintitle'] = 'Simple Quiz';
$string['pluginadministration'] = 'Simple-Quiz-Administration';
$string['modulenameplural'] = 'Simple Quiz';
$string['simplequiz2:addinstance'] = 'Simple-Quiz-Aktivität hinzufügen';
$string['simplequiz2:view'] = 'Simple-Quiz-Aktivität anzeigen';
$string['deletealluserdata'] = 'Alle Simple-Quiz-Versuche löschen';
$string['attemptsdeleted'] = 'Simple-Quiz-Versuche gelöscht';
$string['modulename_help'] = 'Diese Aktivität ermöglicht eine Reihe von Multiple-Choice-Fragen.';

// Edit.
$string['converttoquiz'] = 'In Test-Aktivität umwandeln';
$string['convert_success'] = 'Konvertierung erfolgreich abgeschlossen';
$string['cantconvertcodeerror'] = 'Bei der Konvertierung des Moduls ist ein Fehler aufgetreten. Bitte wenden Sie sich an den Support.';

// Form.
$string['formquestiontitle'] = 'Frage {$a}';
$string['formanswertitle'] = 'Antwort {$a}';
$string['questiontext'] = 'Fragentext';
$string['iscorrectanswer'] = 'Richtige Antwort?';
$string['addquestion'] = 'Frage hinzufügen';
$string['deletequestion'] = 'Diese Frage löschen';
$string['notenoughanswerserror'] = 'Fragen müssen mindestens zwei Antworten haben.';
$string['norightanswererror'] = 'Fragen müssen mindestens eine richtige Antwort haben.';
$string['completionminattemptsgroup'] = 'Mindestanzahl Versuche';
$string['completionminattempts:attempts'] = 'Die/der Lernende muss die Aktivität ein- oder mehrmals abschließen oder versuchen: {$a}';
$string['completionminattempts'] = 'Die/der Lernende muss die Aktivität ein- oder mehrmals abschließen oder versuchen: ';
$string['completionminattemptsdesc'] = 'Die/der Lernende muss die Aktivität {$a}-mal abschließen oder versuchen';

// View.
$string['question'] = 'Frage';
$string['no-questions'] = 'Aktivität in Vorbereitung';
$string['check-answer'] = 'Antwort prüfen';
$string['nextquestion'] = 'Nächste Frage';
$string['restart'] = 'Neu starten';
$string['questionsuccess'] = 'Richtige Antwort';
$string['questionfail'] = 'Falsche Antwort';
$string['questionpartial'] = 'Teilweise richtige Antwort';
$string['aria_audio'] = 'Audio: {$a->description}. Bitte hören Sie den Ton.';
$string['aria_question_text'] = 'Frage {$a->order}: {$a->description}';
$string['aria_answer_text'] = 'Antwort: {$a->answer}';
$string['aria_restart'] = '{$a->status}: Ende der Aktivität. Sie können neu starten oder zur nächsten Aktivität wechseln.';
$string['aria_next'] = '{$a->status}: zur nächsten Frage.';
$string['aria_video'] = 'Video: {$a->description}. Bitte schauen Sie das Video.';
$string['aria_image'] = 'Bild: {$a->description}.';
$string['aria_math'] = 'Mathematische Gleichung.';
$string['aria_no_description'] = 'keine Beschreibung';
$string['session_expired_title'] = 'Sitzung abgelaufen';
$string['session_expired_body'] = '<p>Sie werden zur Startseite weitergeleitet.<br>Bitte melden Sie sich erneut an, um fortzufahren.</p>';
$string['show-results'] = 'Ergebnisse';
$string['result-help'] = 'Nur die beste Punktzahl wird gespeichert.';
$string['result-bestscore'] = 'Beste Punktzahl: {$a->score}%';
$string['result-score'] = 'Erreichte Punktzahl: {$a->score}%';
