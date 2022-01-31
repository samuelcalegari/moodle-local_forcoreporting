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


defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings = new admin_settingpage('local_forcoreporting', get_string('pluginname', 'local_forcoreporting'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configtext('local_forcoreporting/days', get_string('days', 'local_forcoreporting'),
        get_string('configdays', 'local_forcoreporting'), 30, PARAM_INT));

    $settings->add(new admin_setting_configtext('local_forcoreporting/days2', get_string('days2', 'local_forcoreporting'),
        get_string('configdays2', 'local_forcoreporting'), 90, PARAM_INT));
}
