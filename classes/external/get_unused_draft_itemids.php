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
 * External API: allocate fresh user draft item ids for deferred editors.
 *
 * @package    mod_simplequiz2
 * @copyright  2026 Dixeo (contact@dixeo.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_simplequiz2\external;

use context_user;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

/**
 * Return unused draft item ids for resetting question editor fields client-side.
 */
class get_unused_draft_itemids extends external_api {
    /**
     * Describes the external function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Allocate a fresh draft item id for editor fields.
     *
     * @return array
     */
    public static function execute(): array {
        global $USER;

        require_login();
        self::validate_context(context_user::instance($USER->id));

        self::validate_parameters(self::execute_parameters(), []);

        $itemid = file_get_unused_draft_itemid();

        return ['itemid' => $itemid];
    }

    /**
     * Describes the external function return values.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'itemid' => new external_value(PARAM_INT, 'Draft item id'),
        ]);
    }
}
