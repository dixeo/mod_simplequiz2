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
 * Strings for component 'simplequiz2', language 'pt'.
 *
 * @package    mod_simplequiz2
 * @copyright  2024 Dixeo (contact@dixeo.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Generic.
$string['pluginname'] = 'QCM';
$string['modulename'] = 'QCM';
$string['plugintitle'] = 'QCM';
$string['pluginadministration'] = 'Administração do QCM';
$string['modulenameplural'] = 'QCMs';
$string['simplequiz2:addinstance'] = 'Adicionar uma atividade QCM';
$string['simplequiz2:view'] = 'Ver uma atividade QCM';
$string['deletealluserdata'] = 'Eliminar todas as tentativas do QCM';
$string['attemptsdeleted'] = 'Tentativas do QCM eliminadas';
$string['modulename_help'] = 'Esta atividade permite realizar uma série de perguntas de escolha múltipla.';

// Edit.
$string['converttoquiz'] = 'Converter em atividade Teste';
$string['convert_success'] = 'Conversão concluída com sucesso';
$string['cantconvertcodeerror'] = 'Ocorreu um erro ao converter o módulo; contacte a equipa de suporte.';

// Form.
$string['formquestiontitle'] = 'Pergunta {$a}';
$string['formanswertitle'] = 'Resposta {$a}';
$string['questiontext'] = 'Texto da pergunta';
$string['iscorrectanswer'] = 'Resposta correta?';
$string['addquestion'] = 'Adicionar uma pergunta';
$string['deletequestion'] = 'Eliminar esta pergunta';
$string['notenoughanswerserror'] = 'As perguntas devem ter pelo menos duas respostas.';
$string['norightanswererror'] = 'As perguntas devem ter pelo menos uma resposta correta.';
$string['completionminattemptsgroup'] = 'Número mínimo de tentativas';
$string['completionminattempts:attempts'] = 'O formando deve completar ou tentar a atividade uma ou mais vezes: {$a}';
$string['completionminattempts'] = 'O formando deve completar ou tentar a atividade uma ou mais vezes: ';
$string['completionminattemptsdesc'] = 'O formando deve completar ou tentar a atividade {$a} vezes.';

// View.
$string['question'] = 'Pergunta';
$string['no-questions'] = 'Atividade em construção';
$string['check-answer'] = 'Verificar a resposta';
$string['nextquestion'] = 'Pergunta seguinte';
$string['restart'] = 'Reiniciar';
$string['questionsuccess'] = 'Resposta correta';
$string['questionfail'] = 'Resposta incorreta';
$string['questionpartial'] = 'Resposta parcialmente correta';
$string['aria_audio'] = 'Áudio: {$a->description}. Por favor, ouça o som.';
$string['aria_question_text'] = 'Pergunta {$a->order}: {$a->description}';
$string['aria_answer_text'] = 'Resposta: {$a->answer}';
$string['aria_restart'] = '{$a->status}: fim da atividade. Pode reiniciar ou passar à atividade seguinte.';
$string['aria_next'] = '{$a->status}: ir para a pergunta seguinte.';
$string['aria_video'] = 'Vídeo: {$a->description}. Por favor, visualize o vídeo.';
$string['aria_image'] = 'Imagem: {$a->description}.';
$string['aria_math'] = 'Equação matemática.';
$string['aria_no_description'] = 'sem descrição';
$string['session_expired_title'] = 'Sessão expirada';
$string['session_expired_body'] = '<p>Será redirecionado para a página inicial.<br>Inicie sessão novamente para continuar.</p>';
$string['show-results'] = 'Resultados';
$string['result-help'] = 'Apenas a melhor pontuação é guardada.';
$string['result-bestscore'] = 'Melhor pontuação: {$a->score}%';
$string['result-score'] = 'Pontuação obtida: {$a->score}%';
