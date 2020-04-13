<?php

/**
 * List of plugin's observers.
 *
 * @package     local_translate
 * @copyright   2020 Igor <sat.igor.khilman@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$observers = [
    [
        'eventname' => '\core\event\course_module_updated',
        'callback' => '\local_translate\event\observer::course_module_updated'
    ],
    [
        'eventname' => '\core\event\course_module_created',
        'callback' => '\local_translate\event\observer::course_module_created'
    ],
    [
        'eventname' => '\core\event\course_module_deleted',
        'callback' => '\local_translate\event\observer::course_module_deleted'
    ],
    [
        'eventname' => '\core\event\course_section_updated',
        'callback' => '\local_translate\event\observer::course_section_updated'
    ],
    [
        'eventname' => '\core\event\course_section_deleted',
        'callback' => '\local_translate\event\observer::course_section_deleted'
    ],
];
