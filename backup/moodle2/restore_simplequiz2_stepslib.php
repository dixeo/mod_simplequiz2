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
 * Define all the restore steps that will be used by the restore_simplequiz_activity_task
 */

/**
 * Structure step to restore one simplequiz activity.
 */
class restore_simplequiz2_activity_structure_step extends restore_activity_structure_step {

    /**
     * @return array
     */
    protected function define_structure() {

        $paths   = array();
        $paths[] = new restore_path_element('simplequiz2', '/activity/simplequiz2');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * @param $data
     * @throws base_step_exception
     * @throws dml_exception
     */
    protected function process_simplequiz2($data) {
        global $DB;

        $data         = (object) $data;
        $oldid        = $data->id;
        $data->course = $this->get_courseid();

        // Insert the simplequiz record.
        $newitemid = $DB->insert_record('simplequiz2', $data);

        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    protected function after_execute() {
        // Move files into the restored plugin location.
        $this->add_related_files('mod_simplequiz2', 'intro', null);
        $this->add_related_files('mod_simplequiz2', 'data', null);
    }

}
