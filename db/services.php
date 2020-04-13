<?php

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
 * Web service local plugin template external functions and service definitions.
 *
 * @package     local_translate
 * @copyright   2020 Igor <sat.igor.khilman@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
    'local_translate_get_courses' => array(
        'classname' => 'local_translate_external',
        'methodname' => 'get_courses',
        'classpath' => 'local/translate/externallib.php',
        'description' => 'Get courses',
        'type' => 'read',
    ),
    'local_translate_set_course' => array(
        'classname' => 'local_translate_external',
        'methodname' => 'set_course',
        'classpath' => 'local/translate/externallib.php',
        'description' => 'Set course',
        'type' => 'write',
    ),
    'local_translate_get_list_courses' => array(
        'classname' => 'local_translate_external',
        'methodname' => 'get_list_courses',
        'classpath' => 'local/translate/externallib.php',
        'description' => 'get list courses',
        'type' => 'read',
    ),
    'local_translate_get_image_by_name' => array(
        'classname' => 'local_translate_external',
        'methodname' => 'get_image_by_name',
        'classpath' => 'local/translate/externallib.php',
        'description' => 'Get image by name',
        'type' => 'read',
    )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'Local translate' => array(
        'functions' => array('local_translate_get_courses', 'local_translate_set_course', 'local_translate_get_image_by_name', 'local_translate_get_list_courses'),
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);
