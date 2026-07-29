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
 * Tests for pluginfile access control (SQ2-SEC-002).
 *
 * @package    mod_simplequiz2
 * @category   test
 * @copyright  2026 Edunao SAS (contact@edunao.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class pluginfile_access_test extends \advanced_testcase {
    /**
     * Set up each test case.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Enrolled users without mod/simplequiz2:view must not load attachments via pluginfile.
     *
     * @covers ::simplequiz2_pluginfile
     */
    public function test_pluginfile_denies_when_view_prohibited(): void {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/mod/simplequiz2/lib.php');

        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('simplequiz2', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);
        $cm = get_coursemodule_from_id('simplequiz2', $quiz->cmid);
        $course = get_course($cm->course);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);

        assign_capability('mod/simplequiz2:view', CAP_PROHIBIT, $studentroleid, $context->id);

        $fs = get_file_storage();
        $fs->create_file_from_string([
            'contextid' => $context->id,
            'component' => 'mod_simplequiz2',
            'filearea' => 'data',
            'itemid' => 1,
            'filepath' => '/',
            'filename' => 'attachment.png',
        ], 'png');

        $this->setUser($student);

        $this->expectException(\moodle_exception::class);
        simplequiz2_pluginfile($course, $cm, $context, 'data', [1, 'attachment.png'], false);
    }

    /**
     * Users with mod/simplequiz2:view pass the capability gate before file resolution.
     *
     * @covers ::simplequiz2_pluginfile
     */
    public function test_pluginfile_allows_view_capability_before_file_lookup(): void {
        global $CFG;

        require_once($CFG->dirroot . '/mod/simplequiz2/lib.php');

        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('simplequiz2', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);
        $cm = get_coursemodule_from_id('simplequiz2', $quiz->cmid);
        $course = get_course($cm->course);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($student);

        $result = simplequiz2_pluginfile($course, $cm, $context, 'data', [1, 'missing.png'], false);
        $this->assertFalse($result);
    }
}
