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

namespace mod_simplequiz2;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/simplequiz2/lib.php');
require_once($CFG->libdir . '/phpunit/classes/restore_date_testcase.php');

/**
 * Backup and restore tests for mod_simplequiz2.
 *
 * @package    mod_simplequiz2
 * @category   test
 * @copyright  2026 Edunao SAS (contact@edunao.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \backup_simplequiz2_activity_task
 * @covers \restore_simplequiz2_activity_task
 */
final class backup_restore_test extends \restore_date_testcase {
    /**
     * Combined feedback fields survive course backup and restore.
     */
    public function test_backup_restore_preserves_feedback_fields(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_simplequiz2');

        $questionsjson = json_encode([
            (object) [
                'text' => '<p>Question one</p>',
                'correctfeedback' => '<p>Great job!</p>',
                'partiallycorrectfeedback' => '<p>Almost!</p>',
                'incorrectfeedback' => '<p>Not quite.</p>',
                'answers' => [
                    (object) ['text' => '<p>Answer A</p>', 'iscorrect' => 1],
                    (object) ['text' => '<p>Answer B</p>', 'iscorrect' => 0],
                ],
            ],
        ]);

        $quiz = $generator->create_instance([
            'course' => $course->id,
            'name' => 'Feedback quiz',
            'questions' => $questionsjson,
        ]);

        $newcourseid = $this->backup_and_restore($course);

        $restored = $DB->get_record('simplequiz2', ['course' => $newcourseid, 'name' => 'Feedback quiz'], '*', MUST_EXIST);
        $questions = json_decode($restored->questions);
        $this->assertCount(1, $questions);

        $question = simplequiz2_normalize_question($questions[0]);
        $this->assertStringContainsString('Great job!', $question->correctfeedback);
        $this->assertStringContainsString('Almost!', $question->partiallycorrectfeedback);
        $this->assertStringContainsString('Not quite.', $question->incorrectfeedback);
    }

    /**
     * Embedded files in combined feedback are included in backup and restore.
     */
    public function test_backup_restore_preserves_feedback_files(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_simplequiz2');

        $quiz = $generator->create_instance([
            'course' => $course->id,
            'name' => 'Feedback files quiz',
        ]);
        $cm = get_coursemodule_from_instance('simplequiz2', $quiz->id, $course->id, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);

        $fs = get_file_storage();
        $questionitemid = 1;
        $correctitemid = simplequiz2_correct_feedback_itemid($questionitemid);
        $fs->create_file_from_string([
            'contextid' => $context->id,
            'component' => 'mod_simplequiz2',
            'filearea' => 'data',
            'itemid' => $correctitemid,
            'filepath' => '/',
            'filename' => 'correct.png',
        ], 'correct image bytes');

        $questionsjson = json_encode([
            (object) [
                'text' => '<p>Question one</p>',
                'correctfeedback' => '<p>Great job! @@PLUGINFILE@@/correct.png</p>',
                'partiallycorrectfeedback' => '',
                'incorrectfeedback' => '',
                'answers' => [
                    (object) ['text' => '<p>Answer A</p>', 'iscorrect' => 1],
                    (object) ['text' => '<p>Answer B</p>', 'iscorrect' => 0],
                ],
            ],
        ]);
        $DB->set_field('simplequiz2', 'questions', $questionsjson, ['id' => $quiz->id]);

        $newcourseid = $this->backup_and_restore($course);

        $restored = $DB->get_record('simplequiz2', ['course' => $newcourseid, 'name' => 'Feedback files quiz'], '*', MUST_EXIST);
        $restoredcm = get_coursemodule_from_instance('simplequiz2', $restored->id, $newcourseid, false, MUST_EXIST);
        $restoredcontext = \context_module::instance($restoredcm->id);

        $question = simplequiz2_normalize_question(json_decode($restored->questions)[0]);
        $this->assertStringContainsString('@@PLUGINFILE@@/correct.png', $question->correctfeedback);

        $restoredfile = $fs->get_file(
            $restoredcontext->id,
            'mod_simplequiz2',
            'data',
            $correctitemid,
            '/',
            'correct.png'
        );
        $this->assertNotFalse($restoredfile);
        $this->assertSame('correct image bytes', $restoredfile->get_content());
    }
}
