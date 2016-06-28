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
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\task;

/**
 * Scheduled task to refresh the system API user's refresh token.
 */
class refreshsystemrefreshtoken extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_refreshsystemrefreshtoken', 'local_o365');
    }

    /**
     * Attempt token refresh.
     */
    public function execute() {
        if (\local_o365\utils::is_configured() !== true) {
            return false;
        }

        $httpclient = new \local_o365\httpclient();
        $clientdata = \local_o365\oauth2\clientdata::instance_from_oidc();
        $graphresource = \local_o365\rest\azuread::get_resource();
        $systemtoken = \local_o365\oauth2\systemtoken::get_for_new_resource(null, $graphresource, $clientdata, $httpclient);
        if (\local_o365\utils::is_configured_apponlyaccess()) {
            $clientdata = \local_o365\oauth2\clientdata::instance_from_oidc(true);
            $resource = \local_o365\rest\unified::get_resource();
            \local_o365\oauth2\apponlytoken::instance(null, $resource, $clientdata, $httpclient);
        }
        return true;
    }
}
