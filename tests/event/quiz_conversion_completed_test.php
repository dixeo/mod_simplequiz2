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

namespace mod_simplequiz2\event;

/**
 * Quiz conversion event tests.
 *
 * @package    mod_simplequiz2
 * @category   test
 * @copyright  2026 Edunao SAS (contact@edunao.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class quiz_conversion_completed_test extends \advanced_testcase {
    /**
     * Set up each test case.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Quiz conversion completed event carries source context and new quiz id.
     *
     * @covers \mod_simplequiz2\event\quiz_conversion_completed::create_from_conversion
     */
    public function test_quiz_conversion_completed(): void {
        global $DB;

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $simplequizmodule = $this->getDataGenerator()->create_module('simplequiz2', ['course' => $course->id]);
        $quizmodule = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);

        $sourcecm = get_coursemodule_from_id('simplequiz2', $simplequizmodule->cmid, 0, false, MUST_EXIST);
        $simplequiz = $DB->get_record('simplequiz2', ['id' => $simplequizmodule->id], '*', MUST_EXIST);
        $context = \context_module::instance($sourcecm->id);
        $quizcm = get_coursemodule_from_id('quiz', $quizmodule->cmid, 0, false, MUST_EXIST);
        $quiz = $DB->get_record('quiz', ['id' => $quizmodule->id], '*', MUST_EXIST);

        $event = quiz_conversion_completed::create_from_conversion(
            $simplequiz,
            $context,
            $sourcecm,
            $quiz,
            $quizcm
        );

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $triggered = reset($events);

        $this->assertInstanceOf(quiz_conversion_completed::class, $triggered);
        $this->assertEquals($context, $triggered->get_context());
        $this->assertEquals($quiz->id, $triggered->objectid);
        $this->assertEquals($sourcecm->id, $triggered->other['sourcecmid']);
        $this->assertEquals($quizcm->id, $triggered->other['quizcmid']);
    }
}
