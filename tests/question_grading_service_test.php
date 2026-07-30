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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Unit tests for question_grading_service.
 *
 * @package    mod_simplequiz2
 * @category   test
 * @copyright  2026 Dixeo (contact@dixeo.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_simplequiz2;

/**
 * Tests for {@see question_grading_service}.
 */
final class question_grading_service_test extends \advanced_testcase {
    /**
     * Build a sample question object.
     *
     * @return \stdClass
     */
    protected function sample_question(): \stdClass {
        return (object) [
            'text' => 'Pick one',
            'correctfeedback' => 'Yes',
            'partiallycorrectfeedback' => 'Partly',
            'incorrectfeedback' => 'No',
            'answers' => [
                (object) ['text' => 'A', 'iscorrect' => 1],
                (object) ['text' => 'B', 'iscorrect' => 0],
            ],
        ];
    }

    /**
     * Fully correct single selection.
     *
     * @covers \mod_simplequiz2\question_grading_service::grade_question
     */
    public function test_grade_question_correct(): void {
        $grading = question_grading_service::grade_question($this->sample_question(), '0');
        $this->assertTrue($grading['iscorrect']);
        $this->assertFalse($grading['haspartialcorrect']);
        $this->assertEquals([0 => true], $grading['results']);
    }

    /**
     * Wrong selection only.
     *
     * @covers \mod_simplequiz2\question_grading_service::grade_question
     */
    public function test_grade_question_incorrect(): void {
        $grading = question_grading_service::grade_question($this->sample_question(), '1');
        $this->assertFalse($grading['iscorrect']);
        $this->assertFalse($grading['haspartialcorrect']);
        $this->assertEquals([1 => false], $grading['results']);
    }

    /**
     * Empty selection is incorrect.
     *
     * @covers \mod_simplequiz2\question_grading_service::grade_question
     */
    public function test_grade_question_empty(): void {
        $grading = question_grading_service::grade_question($this->sample_question(), '');
        $this->assertFalse($grading['iscorrect']);
        $this->assertFalse($grading['haspartialcorrect']);
        $this->assertSame([], $grading['results']);
    }

    /**
     * first_correct_answer_plaintext returns stripped answer text.
     *
     * @covers \mod_simplequiz2\question_grading_service::first_correct_answer_plaintext
     */
    public function test_first_correct_answer_plaintext(): void {
        $text = question_grading_service::first_correct_answer_plaintext($this->sample_question());
        $this->assertSame('A', $text);
    }
}
