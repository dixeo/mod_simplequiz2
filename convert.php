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
 * Redirection page to create a quiz from an simplequiz activity
 *
 * @package    mod_simplequiz2
 * @copyright 2022 Ministère de l'Éducation nationale français
 * @author     Céline Hernandez
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/mod/simplequiz2/classes/export_to_quiz.php');

require_login();

$cmid = required_param('cmid', PARAM_INT);
$cm   = get_coursemodule_from_id('simplequiz2', $cmid);
require_capability('mod/quiz:addinstance', context_module::instance($cmid));

// Return to course page after duplication.
$returnurl = new moodle_url('/course/view.php', ['id' => $cm->course]);

try {
    // ELEA_RQM-234 : change course format if course is singleactivity
    $course = get_course($cm->course);
    if($course->format == "singleactivity"){
        $coursedata = (object)[
                'id' => $course->id,
                'format' => 'topics'
        ];
        update_course($coursedata);

        // Remove section 1 to only use section 0
        $DB->delete_records('course_sections', ['course' => $course->id, 'section' => 1]);
    }

    // Export current simplequiz to quiz.
    $export = new \mod_simplequiz2\export_to_quiz($cmid);
    $export->export_to_quiz();

    redirect(
        $returnurl,
        get_string('convert_success', 'simplequiz2'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
} catch (Exception $e) {
    redirect($returnurl, get_string('cantconvertcodeerror', 'simplequiz2'));
}
