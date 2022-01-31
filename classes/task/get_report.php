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
 * @package   local_forcoreporting
 * @copyright 2022 Samuel Calegari <samuel.calegari@univ-perp.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_forcoreporting\task;

defined('MOODLE_INTERNAL') || die();

class get_report extends \core\task\scheduled_task {

    /**
     * Get the task name
     * @return string Name
     */
    public function get_name() {
        return get_string('plugindesc', 'local_forcoreporting');
    }

    /**
     * Execute the task
     * @return void
     */
    public function execute() {
        global $CFG, $DB;
        $now = time();
        $days = get_config('local_forcoreporting', 'days');
        $days2 = get_config('local_forcoreporting', 'days2');

        $headers =
            "From: " . $CFG->noreplyaddress . "\r\n" .
            "Reply-To: " . $CFG->noreplyaddress . "\r\n" .
            "Content-Type: text/html; charset='utf-8' \r\n" .
            "X-Mailer: PHP/" . phpversion();

        $subject = 'Rapport de connexion';

        // Listing de tous les enseignants
        $query  = "SELECT Distinct u.id as uid, u.email, COUNT(name) as number, ca.id as cat_id 
                   FROM mdl_course as c, mdl_role_assignments AS ra, mdl_user AS u, mdl_context AS ct, mdl_course_categories ca 
                   WHERE c.id = ct.instanceid AND ra.roleid =3 AND ra.userid = u.id AND ct.id = ra.contextid AND ca.id = c.category GROUP by u.lastname";

        $result = $DB->get_records_sql($query);

        foreach($result as $record){

            if (filter_var($record->email, FILTER_VALIDATE_EMAIL)) {

                 // Listing des étudiants de l'enseignant
                $query = "SELECT userid, contextid, lastname, firstname, lastaccess 
                       FROM mdl_role_assignments left join mdl_user  on mdl_role_assignments.userid = mdl_user.id  
                       WHERE contextid IN (SELECT contextid FROM mdl_role_assignments WHERE userid='$record->uid' AND contextid IN (SELECT id from mdl_context c WHERE c.contextlevel='50')) AND roleid='5'";

                $result2 = $DB->get_records_sql($query);
                $msg = '<html><body>'. "\n";
                $msg.= '<p>Liste des élèves:</p>'. "\n";
                $msg.= '<table rules="all" style="border-color: #666; font-size: 12px;" cellpadding="10">'. "\n";
                $msg.= '<tr style="background: #eee;"><td><strong>Nom</strong></td><td><strong>Dernier accès</strong></td></tr>'. "\n";
                foreach ($result2 as $record2) {

                    $style = 'background: #FFFFFF;';
                    if($record2->lastaccess < $now - ($days2*24*60*60))
                        $style = 'background: #C70039;';
                    else if($record2->lastaccess < $now - ($days*24*60*60))
                        $style = 'background: #FF5733;';

                    $lastaccess = $record2->lastaccess == 0 ? 'Non Connecté' : date("d/m/Y à H:i", $record2->lastaccess). "\n";
                    $msg.= '<tr style="' . $style . '"><td>' . $record2->lastname . ' ' . $record2->firstname . '</td><td>' . $lastaccess . '</td></tr>'. "\n";
                    }
                $msg .= '</table>'. "\n";
                $msg .= '<p>Merci de ne pas répondre à cet email.</p>'. "\n";
                $msg .= '</body></html>'. "\n";

               // mail('samuel.calegari@univ-perp.fr', $subject, $msg, $headers);
               mail($record->email, $subject, $msg, $headers);
            }
        }

    }
}
