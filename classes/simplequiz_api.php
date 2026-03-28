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
 * Simplequiz controller for HTTP async requests.
 *
 * @package    mod_simplequiz2
 * @copyright  2022 Ministère de l'Éducation nationale français; Dixeo (contact@dixeo.com)
 * @author     Céline Hernandez
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_simplequiz2;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/simplequiz2/classes/mod_api.php');
require_once($CFG->dirroot . '/mod/simplequiz2/lib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->libdir . '/gradelib.php');

/**
 * HTTP endpoint implementation for simplequiz2 AJAX actions.
 */
class simplequiz_api extends mod_api {

    /**
     * Ensure the attempt row belongs to the current user and this activity.
     *
     * @param int $attemptid
     * @param simplequiz $simplequiz
     * @return void
     */
    protected function assert_attempt_belongs_to_user(int $attemptid, simplequiz $simplequiz): void {
        global $USER;

        $attemptrow = $this->db->get_attempt_data($attemptid);
        $cm = $simplequiz->__get('cm');
        if (!$attemptrow || (int) $attemptrow->userid !== (int) $USER->id ||
                (int) $attemptrow->cmid !== (int) $cm->id) {
            $this->send(403, 'Forbidden: invalid attempt access.');
        }
    }

    /**
     * Authenticate, enrol check, then dispatch the requested action.
     */
    public function __construct() {
        parent::__construct();

        // User must be logged.
        $this->user_is_logged();

        // User must be enrolled to the course module.
        $this->user_is_enrolled('simplequiz2');

        // Run method controller.
        $this->run();
    }

    /**
     * Check if user answers for a question are correct and return
     * iscorrect : true/false
     * results : status of each selected questions
     *
     * @return void
     */
    protected function check_question() {
        // Get params.
        $cmid = $this->get_param('coursemoduleid', FILTER_VALIDATE_INT);
        $questionid = $this->get_param('questionid', FILTER_VALIDATE_INT);
        $attemptid = $this->get_param('attemptid', FILTER_VALIDATE_INT);
        $useranwsers = $this->get_param('answers');

        $results = [];
        $iscorrect = true;

        $simplequiz = new simplequiz($cmid);
        $this->assert_attempt_belongs_to_user($attemptid, $simplequiz);

        // One correct answers is required per question.
        if ($useranwsers == '') {
            $iscorrect = false;
        } else {
            // Get simplequiz data.
            $questiondata = (array) json_decode($simplequiz->__get('instance')->questions);

            // Prepare question data.
            $question = $questiondata[$questionid];
            $useranwsers = explode(',', $useranwsers);

            foreach ($question->answers as $answerid => $answer) {
                // If the answer is correct, user must have selected it.
                if ($answer->iscorrect == 1 && !in_array($answerid, $useranwsers)) {
                    $iscorrect = false;
                    continue;
                }

                // If the answers if not correct, user must have not selected it.
                if ($answer->iscorrect == 0 && in_array($answerid, $useranwsers)) {
                    $iscorrect = false;
                    $results[$answerid] = false;
                    continue;
                }

                // Else the answers if correct.
                if (in_array($answerid, $useranwsers)) {
                    $results[$answerid] = true;
                }
            }
        }

        // Update attempt with the new answer.
        $simplequiz->add_attempt_answer($attemptid, $questionid, $iscorrect);

        // Return data to the client.
        $this->send(200, 'Get question results', [
            // Status of each selected answers.
            'results' => $results,
            // True if answers are corrects.
            'iscorrect' => $iscorrect,
        ]);
    }

    /**
     * Return attempt score and current best grade for the user.
     */
    protected function get_attempt_results() {
        global $USER;

        // Get params.
        $cmid = $this->get_param('coursemoduleid', FILTER_VALIDATE_INT);
        $attemptid = $this->get_param('attemptid', FILTER_VALIDATE_INT);

        $simplequiz = new simplequiz($cmid);
        $this->assert_attempt_belongs_to_user($attemptid, $simplequiz);

        $attemptgrade = $simplequiz->get_attempt_grade($attemptid);
        $currentgrade = $simplequiz->get_current_grade($USER->id);

        // Return data to the client.
        $this->send(200, 'Get attempt results', [
            // Attempt score.
            'attemptgrade' => $attemptgrade,
            // Best score.
            'bestscore' => $currentgrade,
        ]);
    }
}
