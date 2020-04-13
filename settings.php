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
 * Plugin administration pages are defined here.
 *
 * @package     local_translate
 * @category    admin
 * @copyright   2020 Igor <sat.igor.khilman@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings = new admin_settingpage('local_translate', get_string('settings_title', 'local_translate'));

    // Create
    $ADMIN->add('localplugins', $settings);

    $data = get_courses();
    $sort_arr = [];
    foreach ($data as $key => $item) {
        $sort_arr[$key] = format_string($item->fullname);
    }

    $settings->add(
        new admin_setting_configmultiselect(
            'local_translate/translation_courses',
            get_string('courses_to_sync', 'local_translate'),
            get_string('courses_to_sync_desc', 'local_translate'),
            [], $sort_arr
        )
    );
}
