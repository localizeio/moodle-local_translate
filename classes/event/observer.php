<?php
/**
 * observer for course
 */

namespace local_translate\event;


use core\event\course_module_created;
use core\event\course_module_deleted;
use core\event\course_module_updated;
use core\event\course_section_deleted;
use core\event\course_section_updated;

/**
 * Class observer
 * @package local_translate\event
 */
class observer
{
    /**
     * Course Update Event
     * @param $courseid
     * @throws \dml_exception
     */
    protected static function update_course_timemodified($courseid){
        global $DB;
        if(!empty($courseid)){
            $course = $DB->get_record('course', ['id' => $courseid]);
            $course->timemodified = time();
            $DB->update_record('course', $course);
        }
    }

    /**
     * Course Module Update Event
     * @param course_module_updated $event
     * @throws \dml_exception
     */
    public static function course_module_updated(course_module_updated $event){
        $event_data = $event->get_data();
        if(!empty($event_data['courseid'])){
               observer::update_course_timemodified($event_data['courseid']);
        }
    }

    /**
     * Course Module Create Event
     * @param course_module_created $event
     * @throws \dml_exception
     */
    public static function course_module_created(course_module_created $event){
        $event_data = $event->get_data();
        if(!empty($event_data['courseid'])){
            observer::update_course_timemodified($event_data['courseid']);
        }
    }

    /**
     * Course Module Delete Event
     * @param course_module_deleted $event
     * @throws \dml_exception
     */
    public static function course_module_deleted(course_module_deleted $event){
        $event_data = $event->get_data();
        if(!empty($event_data['courseid'])){
            observer::update_course_timemodified($event_data['courseid']);
        }
    }

    /**
     * Course Section Update Event
     * @param course_section_updated $event
     * @throws \dml_exception
     */
    public static function course_section_updated(course_section_updated $event){
        $event_data = $event->get_data();
        if(!empty($event_data['courseid'])){
            observer::update_course_timemodified($event_data['courseid']);
        }
    }

    /**
     * Course Section Delete Event
     * @param course_section_deleted $event
     * @throws \dml_exception
     */
    public static function course_section_deleted(course_section_deleted $event){
        $event_data = $event->get_data();
        if(!empty($event_data['courseid'])){
            observer::update_course_timemodified($event_data['courseid']);
        }
    }
}