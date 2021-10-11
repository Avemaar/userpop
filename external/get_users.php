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

namespace local_userpop\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * Web service used by form autocomplete to get a list of users with a given capability.
 *
 * @package   report_customsql
 * @copyright 2020 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_users extends \external_api {
    
    /**
     * Convert a user object into the form required to return.
     *
     * @param \stdClass $user data on a user.
     * @param array $extrafields extra profile fields to show.
     * @return \stdClass return object.
     */
    public static function prepare_result_object(\stdClass $user, array $extrafields): \stdClass {
        global $PAGE;
        $userpicture = new \user_picture($user);
        $userpicture->size = 0; // Size f2.

        $identity = [];
        foreach ($extrafields as $field) {
            if ($user->$field) {
                $identity[] = $user->$field;
            }
        }
        $identity = implode(', ', $identity);

        return (object) [
                'id' => $user->id,
                'fullname' => fullname($user),
                'identity' => $identity,
                'hasidentity' => (bool) $identity,
                'profileimageurlsmall' => $userpicture->get_url($PAGE)->out(false),
        ];
    }

}
