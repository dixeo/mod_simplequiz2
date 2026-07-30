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

/**
 * Tests for convert.php CSRF protection (SQ2-SEC-001).
 *
 * @package    mod_simplequiz2
 * @category   test
 * @copyright  2026 Edunao SAS (contact@edunao.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversNothing
 */
final class convert_csrf_test extends \advanced_testcase {
    /**
     * Set up each test case.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * GET without confirm must show a confirmation page and must not convert the activity.
     */
    public function test_convert_confirmation_page_without_database_changes(): void {
        global $CFG;

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('simplequiz2', ['course' => $course->id]);
        $quizcountbefore = $this->count_quiz_modules($course->id);

        $_GET = ['cmid' => $quiz->cmid];
        $_POST = [];

        ob_start();
        include($CFG->dirroot . '/mod/simplequiz2/convert.php');
        $output = ob_get_clean();

        $this->assertStringContainsString(get_string('convertconfirm', 'simplequiz2'), $output);
        $this->assertSame($quizcountbefore, $this->count_quiz_modules($course->id));
    }

    /**
     * Confirmed conversion must require a valid session key before any database changes.
     */
    public function test_convert_with_confirm_requires_sesskey(): void {
        global $CFG;

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('simplequiz2', ['course' => $course->id]);
        $quizcountbefore = $this->count_quiz_modules($course->id);

        $_GET = [
            'cmid' => $quiz->cmid,
            'confirm' => 1,
        ];
        $_POST = [];

        try {
            include($CFG->dirroot . '/mod/simplequiz2/convert.php');
            $this->fail('Expected moodle_exception when sesskey is missing');
        } catch (\moodle_exception $e) {
            $this->assertSame('missingparam', $e->errorcode);
        }

        $this->assertSame($quizcountbefore, $this->count_quiz_modules($course->id));
    }

    /**
     * Count quiz course modules in a course.
     *
     * @param int $courseid Course id.
     * @return int
     */
    private function count_quiz_modules(int $courseid): int {
        global $DB;

        $moduleid = $DB->get_field('modules', 'id', ['name' => 'quiz'], MUST_EXIST);
        return $DB->count_records('course_modules', [
            'course' => $courseid,
            'module' => $moduleid,
        ]);
    }
}
