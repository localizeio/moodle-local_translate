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
 * External Web Service Template
 *
 * @package    local_translate
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

/**
 * Class local_translate_external
 */
class local_translate_external extends external_api
{

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_courses_parameters()
    {
        return new external_function_parameters(
            array(
                'cids' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'cid' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
                        ], '', VALUE_DEFAULT, []), '', VALUE_DEFAULT, []
                ),
                'timemodify' => new external_value(PARAM_INT, '', VALUE_DEFAULT,0)
            )
        );
    }

    /**
     * Getting content for courses
     * @param $cids
     * @param $timemodify
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function get_courses($cids, $timemodify)
    {
        $courses = self::get_list_translated_courses($cids, $timemodify);
        $result = [];
        $filters_controller = new \local_translate\filters_controller();
        $filters_controller->off();
        foreach ($courses as $course) {
            $course = new \local_translate\course($course->id);
            $obj = $course->preparingToSend();
            $result[] = $obj;
        }
        $filters_controller->on();
        return $result;
    }

    /**
     * Function to get the id of courses to be translated
     *
     * @param $cids
     * @param $timemodify
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function get_list_translated_courses($cids, $timemodify)
    {
        global $DB;
        $tempdata = [];
        $select = "timemodified > :timemodify ";
        $tempdata['timemodify'] = $timemodify;
        if (!empty($cids)) {
            $cids_arr = [];
            foreach ($cids as $key => $cid) {
                $cids_arr[$key] = $cid['cid'];
            }
            list($querycids, $tempcids) = $DB->get_in_or_equal($cids_arr, SQL_PARAMS_NAMED);
            $select .= "and id {$querycids}";
            $tempdata = array_merge($tempdata, $tempcids);
        }
        $config = get_config('local_translate', 'translation_courses');
        $config = explode(',', $config);
        list($queryconfig, $tempconfig) = $DB->get_in_or_equal($config, SQL_PARAMS_NAMED, 'conf');
        $tempdata = array_merge($tempdata, $tempconfig);
        $select .= " and id {$queryconfig}";
        return $DB->get_records_select('course', $select, $tempdata, '', 'id');
    }


    /**
     * Get type and field
     * @return array
     */
    public static function get_type()
    {
        return $types = [
            'id' => [
                'type' => PARAM_INT,
                'required' => VALUE_REQUIRED
            ],
            'fullname' => [
                'type' => PARAM_RAW,
                'required' => VALUE_OPTIONAL
            ],
            'shortname' => [
                'type' => PARAM_RAW,
                'required' => VALUE_OPTIONAL
            ],
            'summary' => [
                'type' => PARAM_RAW,
                'required' => VALUE_OPTIONAL
            ],
            'sections' => [
                'type' => PARAM_RAW,
                'required' => VALUE_OPTIONAL,
                'multiple' => [
                    'single' => [
                        'instance' => [
                            'type' => PARAM_INT,
                            'required' => VALUE_REQUIRED
                        ],
                        'modname' => [
                            'type' => PARAM_TEXT,
                            'required' => VALUE_REQUIRED
                        ],
                        'name' => [
                            'type' => PARAM_RAW,
                            'required' => VALUE_OPTIONAL
                        ],
                        'summary' => [
                            'type' => PARAM_RAW,
                            'required' => VALUE_OPTIONAL
                        ],
                        'modules' => [
                            'type' => PARAM_RAW,
                            'required' => VALUE_OPTIONAL,
                            'multiple' => [
                                'single' => [
                                    'instance' => [
                                        'type' => PARAM_INT,
                                        'required' => VALUE_REQUIRED
                                    ],
                                    'modname' => [
                                        'type' => PARAM_TEXT,
                                        'required' => VALUE_REQUIRED
                                    ],
                                    'name' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL
                                    ],
                                    'intro' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL
                                    ],
                                    'content' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL
                                    ],
                                    'page_after_submit' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL
                                    ],
                                    'instructauthors' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL
                                    ],
                                    'instructreviewers' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL
                                    ],
                                    'conclusion' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL
                                    ],
                                    'options' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL,
                                        'multiple' => [
                                            'single' => [
                                                'instance' => [
                                                    'type' => PARAM_INT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'modname' => [
                                                    'type' => PARAM_TEXT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'text' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                            ]
                                        ]
                                    ],
                                    'entries' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL,
                                        'multiple' => [
                                            'single' => [
                                                'instance' => [
                                                    'type' => PARAM_INT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'modname' => [
                                                    'type' => PARAM_TEXT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'concept' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                                'definition' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                            ]
                                        ]
                                    ],
                                    'charters' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL,
                                        'multiple' => [
                                            'single' => [
                                                'instance' => [
                                                    'type' => PARAM_INT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'modname' => [
                                                    'type' => PARAM_TEXT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'title' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                                'content' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                            ]
                                        ]
                                    ],
                                    'pages' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL,
                                        'multiple' => [
                                            'single' => [
                                                'instance' => [
                                                    'type' => PARAM_INT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'modname' => [
                                                    'type' => PARAM_TEXT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'title' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                                'contents' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                                'cachedcontent' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                            ]
                                        ]
                                    ],
                                    'accumulative' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL,
                                        'multiple' => [
                                            'single' => [
                                                'instance' => [
                                                    'type' => PARAM_INT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'modname' => [
                                                    'type' => PARAM_TEXT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'description' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                            ]
                                        ]
                                    ],
                                    'answers' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL,
                                        'multiple' => [
                                            'single' => [
                                                'instance' => [
                                                    'type' => PARAM_INT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'modname' => [
                                                    'type' => PARAM_TEXT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'answer' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                                'response' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                            ]
                                        ]
                                    ],
                                    'questions' => [
                                        'type' => PARAM_RAW,
                                        'required' => VALUE_OPTIONAL,
                                        'multiple' => [
                                            'single' => [
                                                'instance' => [
                                                    'type' => PARAM_INT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'modname' => [
                                                    'type' => PARAM_TEXT,
                                                    'required' => VALUE_REQUIRED
                                                ],
                                                'name' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                                'questiontext' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                                'generalfeedback' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL
                                                ],
                                                'hints' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL,
                                                    'multiple' => [
                                                        'single' => [
                                                            'instance' => [
                                                                'type' => PARAM_INT,
                                                                'required' => VALUE_REQUIRED
                                                            ],
                                                            'modname' => [
                                                                'type' => PARAM_TEXT,
                                                                'required' => VALUE_REQUIRED
                                                            ],
                                                            'hint' => [
                                                                'type' => PARAM_RAW,
                                                                'required' => VALUE_OPTIONAL
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                                'params' => [
                                                    'type' => PARAM_RAW,
                                                    'required' => VALUE_OPTIONAL,
                                                    'multiple' => [
                                                        'single' => [
                                                            'instance' => [
                                                                'type' => PARAM_INT,
                                                                'required' => VALUE_REQUIRED
                                                            ],
                                                            'modname' => [
                                                                'type' => PARAM_TEXT,
                                                                'required' => VALUE_REQUIRED
                                                            ],
                                                            'answer' => [
                                                                'type' => PARAM_RAW,
                                                                'required' => VALUE_OPTIONAL
                                                            ],
                                                            'questiontext' => [
                                                                'type' => PARAM_RAW,
                                                                'required' => VALUE_OPTIONAL
                                                            ],
                                                            'feedback' => [
                                                                'type' => PARAM_RAW,
                                                                'required' => VALUE_OPTIONAL
                                                            ],
                                                            'answertext' => [
                                                                'type' => PARAM_RAW,
                                                                'required' => VALUE_OPTIONAL
                                                            ],
                                                            'graderinfo' => [
                                                                'type' => PARAM_RAW,
                                                                'required' => VALUE_OPTIONAL
                                                            ],
                                                            'hint' => [
                                                                'type' => PARAM_RAW,
                                                                'required' => VALUE_OPTIONAL
                                                            ],
                                                            'correctfeedback' => [
                                                                'type' => PARAM_RAW,
                                                                'required' => VALUE_OPTIONAL
                                                            ],
                                                            'partiallycorrectfeedback' => [
                                                                'type' => PARAM_RAW,
                                                                'required' => VALUE_OPTIONAL
                                                            ],
                                                            'incorrectfeedback' => [
                                                                'type' => PARAM_RAW,
                                                                'required' => VALUE_OPTIONAL
                                                            ]
                                                        ]
                                                    ]
                                                ],
                                            ]
                                        ]
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * Formation of an object for validation
     *
     * @param $array
     * @param bool $isReturn
     * @return array|external_function_parameters|external_multiple_structure|external_single_structure
     */
    public static function formation_validation($array,$isReturn = false){
        $temp = [];
        foreach ($array as $item => $value){
            if ($item == 'external_function'){
                $rs = self::formation_validation($value,$isReturn);
                return new external_function_parameters($rs,'',VALUE_OPTIONAL);
            }elseif ($item == 'multiple'){
                $rs = self::formation_validation($value,$isReturn);
                return new external_multiple_structure($rs,'',VALUE_OPTIONAL);
            } elseif ($item == 'single'){
                $rs = self::formation_validation($value,$isReturn);
                return new external_single_structure($rs,'',VALUE_OPTIONAL);
            } elseif (isset($value["multiple"])){
                $rs = self::formation_validation($value["multiple"],$isReturn);
                $ems = new external_multiple_structure($rs,'',VALUE_OPTIONAL);
                $temp[$item] = $ems;
            } else
            {
                if (!$isReturn || array_search($item,['id','instance','modname']) !== false ){
                    $temp[$item] = new external_value($value["type"], $item, $value["required"]);
                }
                else{
                    $temp[$item] = self::get_single_structure_lang($value["type"], $item);
                }

            }

        }
        return $temp;
    }

    /**
     * Function result
     *
     * @return external_multiple_structure
     */
    public static function get_courses_returns()
    {

        $types['multiple']['single'] = self::get_type();
        return self::formation_validation($types);
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function set_course_parameters()
    {
        return new external_function_parameters([]);
    }

    /**
     * Formation of structure output for a field
     * @param $type
     * @param string $desc
     * @return external_single_structure
     */
    public static function get_single_structure_lang($type, $desc = '')
    {
        $structure = [];
        foreach (get_string_manager()->get_list_of_translations() as $key => $lang) {
            $structure[$key] = new external_value($type, "[{$key}] $desc", ($key == 'en') ? VALUE_OPTIONAL : VALUE_REQUIRED);
        }
        return new external_single_structure($structure, $desc, VALUE_OPTIONAL);
    }

    /**
     * Check course parameters
     *
     * @return external_function_parameters
     */
    public static function check_course_parameters()
    {

        $types['external_function'] = self::get_type();
        return self::formation_validation($types,true);
    }

    /**
     * Course filling function
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws invalid_response_exception
     * @throws moodle_exception
     */
    public static function set_course()
    {
        $timestart = time();
        if ($_SERVER["REQUEST_METHOD"] != 'PUT') {
            throw new invalid_response_exception(get_string('method_not_allowed', 'local_translate', $_SERVER["REQUEST_METHOD"]));
        }
        $request_course_raw = (file_get_contents("php://input"));
        $request_course = json_decode($request_course_raw, true);
        if (empty($request_course)) {
            throw new invalid_response_exception(get_string('no_data', 'local_translate'));
        }
        $request_cou = $request_course;
        $params = self::validate_parameters(self::check_course_parameters(), $request_cou);
        global $DB;
        if (!$DB->record_exists('course', ['id' => $params['id']])) {
            throw new invalid_response_exception(get_string('course_id_not_valid', 'local_translate'));
        }

        $course = new \local_translate\course($params['id']);
        $course->generationAndSetBackup();
        $course->set_content($params);

        purge_all_caches();
        $timeend = time();

        return [
            'status' => 'OK',
            'time' => microtime_diff($timestart, $timeend) . 's.',
            'diff' => count($course->response['diff']),
            'error' => json_encode($course->response['error']),
            'updated' => count($course->response['updated'])
        ];
    }


    /**
     * Function result
     * @return external_function_parameters
     */
    public static function set_course_returns()
    {
        return new external_function_parameters(
            [
                'status' => new external_value(PARAM_TEXT, 'status', VALUE_OPTIONAL),
                'time' => new external_value(PARAM_TEXT, 'time', VALUE_OPTIONAL),
                'diff' => new external_value(PARAM_INT, 'diff', VALUE_OPTIONAL),
                'error' => new external_value(PARAM_RAW, 'error', VALUE_OPTIONAL),
                'updated' => new external_value(PARAM_INT, 'updated', VALUE_OPTIONAL)
            ]);
    }

    /**
     * Function to get the image
     * @return external_function_parameters
     */
    public static function get_image_by_name_parameters()
    {
        return new external_function_parameters([
            'name' => new external_value(PARAM_TEXT, 'status', VALUE_OPTIONAL),
        ]);
    }

    /**
     * Function to get the image
     * @param $name
     * @throws dml_exception
     */
    public static function get_image_by_name($name)
    {
        global $DB;
        $name = urldecode($name);
        $files = $DB->get_records('files',['pathnamehash' => $name],'timemodified','*',0,1);
        if ($files){
            $filerecord = reset($files);
        }
        $fs = get_file_storage();
        if (!$file = $fs->get_file($filerecord->contextid, $filerecord->component, $filerecord->filearea, $filerecord->itemid, $filerecord->filepath, $filerecord->filename) || $file->is_directory()) {
            send_file_not_found();
        }

        send_stored_file($file, 10*60, 0);
    }

    /**
     * Function to get the image
     * @return external_function_parameters
     */
    public static function get_image_by_name_returns(){
        return new external_function_parameters(
            [
                'status' => new external_value(PARAM_TEXT, 'status', VALUE_OPTIONAL)
            ]);
    }

    /**
     * Get list courses for translate
     * @return external_function_parameters
     */
    public static function get_list_courses_parameters()
    {
        return new external_function_parameters(
            array(
                'timemodify' => new external_value(PARAM_INT, 'timemodify', VALUE_DEFAULT,0),
            )
        );
    }

    /**
     * Get list courses for translate
     *
     * @param $params
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function get_list_courses($timemodify){
        return self::get_list_translated_courses([],$timemodify);
    }

    /**
     * Get list courses for translate
     *
     * @return external_multiple_structure
     */
    public static function get_list_courses_returns(){
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
                ]
            ));
    }
}
