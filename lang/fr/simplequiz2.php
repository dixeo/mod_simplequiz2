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
 * Strings for component 'simplequiz'
 *
 * @package    mod_simplequiz2
 * @copyright 2022 Ministère de l'Éducation nationale français
 * @author     Céline Hernandez
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Generic.
$string['pluginname'] = 'QCM';
$string['modulename'] = 'QCM';
$string['plugintitle'] = 'QCM';
$string['pluginadministration'] = 'Administration QCM';
$string['modulenameplural'] = 'QCMs';
$string['simplequiz2:addinstance'] = 'Ajouter une activité QCM';
$string['simplequiz2:view'] = 'Voir une activité QCM';
$string['deletealluserdata'] = 'Supprimer toutes les tentatives des QCMs';
$string['attemptsdeleted'] = 'Suppression des tentatives de QCM';
$string['modulename_help'] = 'Cette activité vous permet de réaliser une série de questions à choix multiples.';

// Edit.
$string['converttoquiz'] = 'Convertir en activité Test';
$string['convert_success'] = "Conversion effectuée avec succès";
$string['cantconvertcodeerror']
        = 'Une erreur s\'est produite lors de la conversion du module, veuillez contacter l\'équipe de support';

// Form.
$string['formquestiontitle'] = 'Question {$a}';
$string['formanswertitle'] = 'Réponse {$a}';
$string['questiontext'] = 'Texte de la question';
$string['iscorrectanswer'] = 'Réponse correcte ?';
$string['addquestion'] = "Ajouter une question";
$string['deletequestion'] = "Supprimer cette question";
$string['notenoughanswerserror'] = 'Les questions doivent comporter au moins deux réponses.';
$string['norightanswererror'] = 'Les questions doivent avoir au moins une bonne réponse.';
$string['completionminattemptsgroup'] = 'Nombre de tentatives minimum';
$string['completionminattempts:attempts'] = 'L\'étudiant doit réussir l\'activité ou la tenter une ou plusieurs fois : {$a}';
$string['completionminattempts'] = 'L\'étudiant doit réussir l\'activité ou la tenter une ou plusieurs fois : ';
$string['completionminattemptsdesc'] = 'L\'étudiant doit réussir l\'activité ou la tenter {$a} fois';

// View.
$string['question'] = 'Question';
$string['no-questions'] = "Activité en cours de conception";
$string['check-answer'] = "Vérifier la réponse";
$string['nextquestion'] = "Question suivante";
$string['restart'] = "Recommencer";
$string['questionsuccess'] = "Bonne réponse";
$string['questionfail'] = "Mauvaise réponse";
$string['questionpartial'] = 'Réponse partiellement correcte';
$string['aria_audio'] = 'Audio: {$a->description}. Veuillez écoutez le son.';
$string['aria_question_text'] = 'Question {$a->order}: {$a->description}';
$string['aria_answer_text'] = 'Réponse : {$a->answer}';
$string['aria_restart'] = '{$a->status}: fin de l\'activité. Vous pouvez recommencer ou passer à l\'activité suivante.';
$string['aria_next'] = '{$a->status}: aller à la question suivante.';
$string['aria_video'] = 'Vidéo: {$a->description}. Veuillez visionner la vidéo.';
$string['aria_audio'] = 'Audio: {$a->description}. Veuillez écoutez le son.';
$string['aria_image'] = 'Image: {$a->description}.';
$string['aria_math'] = 'Equation mathématique.';
$string['aria_no_description'] = 'pas de description';
$string['session_expired_title'] = 'Session expirée';
$string['session_expired_body']
        = "<p>Vous allez être redirigé vers la page d'accueil.<br>Merci de vous reconnecter pour continuer.</p>";
// ELEA_RQM-163 : Add result page at the end of the quiz .
$string['show-results'] = 'Résultats';
$string['result-help'] = 'Seul le meilleur score est conservé.';
$string['result-bestscore'] = 'Meilleur score : {$a->score}%';
$string['result-score'] = 'Score obtenu : {$a->score}%';
