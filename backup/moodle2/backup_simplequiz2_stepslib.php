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
 *
 *
 * @package    mod_simplequiz2
 * @copyright 2022 Ministère de l'Éducation nationale français
 * @author     Céline Hernandez
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete simplequiz structure for backup, with file and id annotations
 */
class backup_simplequiz2_activity_structure_step extends backup_activity_structure_step {

    /**
     * @return backup_nested_element
     * @throws base_element_struct_exception
     * @throws base_step_exception
     */
    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $simplequiz = new backup_nested_element(
            'simplequiz2',
            ['id'],
            [
                'course',
                'name',
                'intro',
                'introformat',
                'questions',
                'grade',
                'timecreated',
                'timemodified',
                'completionminattempts'
            ]
        );

        // Define sources.
        $simplequiz->set_source_table('simplequiz2', ['id' => backup::VAR_ACTIVITYID]);

        // Define file.
        $simplequiz->annotate_files('mod_simplequiz2', 'intro', null);
        $simplequiz->annotate_files('mod_simplequiz2', 'data', null);

        // Return the root element (simplequiz), wrapped into standard activity structure.
        return $this->prepare_activity_structure($simplequiz);
    }
}
