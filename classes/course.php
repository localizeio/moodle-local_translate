<?php
/**
 * Class for working with the course
 *
 * @package    local_translate
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_translate;

/**
 * Class Course
 * @package local_translate
 */
class course
{

    /**
     * Id course
     * @var integer
     */
    public $course;

    /**
     * Course Information
     * @var
     */
    public $modinfo;

    /**
     * Course sections
     * @var
     */
    public $sections;
    /**
     * Course modules
     * @var
     */
    public $cms;
    /**
     * Course modules
     * @var
     */
    public $coursesections;
    /**
     * Course instances
     * @var
     */
    public $instances;

    /**
     * Backup course
     * @var
     */
    public $backup;

    /**
     * All active languages
     * @var
     */
    public $langs;

    /**
     * Modules
     * @var array
     */
    public $modules;
    /**
     * List of languages for content translation when preparing data
     *
     * @var string | array
     */
    public $contentlangs = 'en';

    /**
     * Respons
     * @var array
     */
    public $response = [
        'error' => [],
        'updated' => [],
        'diff' => []
    ];

    /**
     * Fields to be ignored when writing to the database
     *
     * @var array
     */
    private $_ignorefiedls  = ['id', 'instance', 'modname'];

    /**
     * course constructor.
     * @param $id
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    function __construct($id)
    {
        require_once('../../config.php');
        if (empty($id)) {
            return null;
        }
        global $DB;
        $this->modules = $DB->get_records_menu('modules', [], '', 'name,id');
        $this->langs = get_string_manager()->get_list_of_translations();
        asort($this->langs);
        $this->loadCourse($id);

    }

    /**
     * Backup for the course
     *
     * @throws \dml_exception
     */
    function generationAndSetBackup()
    {

        $this->setContentLangs($this->langs);
        $this->backup = json_decode(json_encode($this->preparingToSend()), true);
        $this->setContentLangs();
    }

    /**
     * Setting the list of languages
     * @param $langs
     */
    function setContentLangs($langs = 'en')
    {
        $this->contentlangs = $langs;
    }

    /**
     * Preparing content for an response
     *
     * @return \stdClass
     * @throws \dml_exception
     */
    function preparingToSend()
    {
        global $DB;

        $obj_course = new \stdClass();
        $obj_course->id = $this->course->id;
        $contextcourseid = \context_course::instance($this->course->id)->id;
        $obj_course->fullname = $this->formatText($this->course->fullname);
        $obj_course->shortname = $this->formatText($this->course->shortname, $contextcourseid);
        $obj_course->summary = $this->formatText($this->course->summary, $contextcourseid);

        $obj_course->sections = [];
        foreach ($this->sections as $section) {
            if ($section->visible != 1) {
                continue;
            }
            $obj_sections = new \stdClass();
            $obj_sections->instance = $section->id;
            $obj_sections->modname = 'course_sections';
            $obj_sections->name = $this->formatText($section->name, $contextcourseid);
            $obj_sections->summary = $this->formatText($section->summary, $contextcourseid);
            $obj_sections->modules = [];

            if (isset($this->coursesections[$section->section])) {
                foreach ($this->coursesections[$section->section] as $coursesection) {
                    $module = new \stdClass();
                    $module->instance = $this->cms[$coursesection]->instance;
                    $type = $this->cms[$coursesection]->modname;
                    $module->modname = $type;
                    $cmid = $DB->get_field('course_modules', 'id', ['module' => $this->modules[$type], 'instance' => $module->instance, 'visible' => 1]);
                    if (empty($cmid)) {
                        continue;
                    }
                    $contexmodtid = \context_module::instance($cmid)->id;
                    $module->name = $this->formatString($this->cms[$coursesection]->name);
                    $module->intro = $this->formatText($this->instances[$module->modname][$this->cms[$coursesection]->instance]->intro, $contexmodtid);
                    if (isset($this->instances[$type]) && isset($this->instances[$module->modname][$module->instance])) {
                        $method = "getType{$type}Content";
                        if (method_exists($this, $method)) {
                            $this->$method($module, $coursesection, $contexmodtid);
                        }

                    }
                    $obj_sections->modules[] = $module;
                }
            }
            $obj_course->sections[] = $obj_sections;
        }
        return ($obj_course);
    }

    /**
     * Getting data for the module
     * @param $module
     * @param $id
     * @param $contextid
     */
    function getTypeWorkshopContent(&$module, $id, $contextid)
    {
        $workshop = $this->instances[$module->modname][$this->cms[$id]->instance];
        $module->instructauthors =  $this->formatText($workshop->instructauthors);
        $module->instructreviewers =  $this->formatText($workshop->instructreviewers);
        $module->conclusion =  $this->formatText($workshop->conclusion);
        $module->accumulative = [];
        foreach ($workshop->accumulative as $accumulative){
            $module->accumulative[] = [
                'instance' => $accumulative->instance,
                'modname' => $accumulative->modname,
                'description' => $this->formatText($accumulative->description),
            ];
        }
    }

    /**
     * Getting data for the module
     * @param $module
     * @param $id
     * @param $contextid
     */
    function getTypeWikiContent(&$module, $id, $contextid)
    {
        $wiki = $this->instances[$module->modname][$this->cms[$id]->instance];
        $module->pages = [];
        foreach ($wiki->pages as $page){
            $module->pages[] = [
                'instance' => $page->instance,
                'modname' => $page->modname,
                'title' => $this->formatString($page->title),
                'cachedcontent' => $this->formatText($page->cachedcontent),
            ];
        }
    }
    /**
     * Getting data for the module
     * @param $module
     * @param $id
     * @param $contextid
     */
    function getTypeBookContent(&$module, $id, $contextid)
    {
        $book = $this->instances[$module->modname][$this->cms[$id]->instance];
        $module->charters = [];
        foreach ($book->charters as $charter){
            $module->charters[] = [
                'instance' => $charter->instance,
                'modname' => $charter->modname,
                'title' => $this->formatString($charter->title),
                'content' => $this->formatText($charter->content),
            ];
        }
    }
    /**
     * Getting data for the module
     * @param $module
     * @param $id
     * @param $contextid
     */
    function getTypeLessonContent(&$module, $id, $contextid)
    {
        $lesson = $this->instances[$module->modname][$this->cms[$id]->instance];
        $module->pages = [];
        foreach ($lesson->pages as $page){
            $module->pages[] = [
                'instance' => $page->instance,
                'modname' => $page->modname,
                'title' => $this->formatString($page->title),
                'contents' => $this->formatText($page->contents),
            ];
        }
        $module->answers = [];
        foreach ($lesson->answers as $answer){
            $module->answers[] = [
                'instance' => $answer->instance,
                'modname' => $answer->modname,
                'answer' => $this->formatString($answer->answer),
                'response' => $this->formatText($answer->response),
            ];
        }
    }

    /**
     * Getting data for the module
     * @param $module
     * @param $id
     * @param $contextid
     */
    function getTypeGlossaryContent(&$module, $id, $contextid)
    {
        $glossary = $this->instances[$module->modname][$this->cms[$id]->instance];
        $module->entries = [];
        foreach ($glossary->entries as $entry){
            $module->entries[] = [
                'instance' => $entry->instance,
                'modname' => $entry->modname,
                'concept' => $this->formatString($entry->concept),
                'definition' => $this->formatText($entry->definition),
            ];
        }
    }
    /**
     * Getting data for the module
     * @param $module
     * @param $id
     * @param $contextid
     */
    function getTypeFeedbackContent(&$module, $id, $contextid)
    {
        $feedback = $this->instances[$module->modname][$this->cms[$id]->instance];
        $module->page_after_submit = $this->formatText($feedback->page_after_submit);
    }
    /**
     * Getting data for the module
     * @param $module
     * @param $id
     * @param $contextid
     */
    function getTypeChoiceContent(&$module, $id, $contextid)
    {
        $choice = $this->instances[$module->modname][$this->cms[$id]->instance];
        $module->options = [];
        foreach ($choice->options as $option){
            $module->options[] = [
                'instance' => $option->instance,
                'modname' => $option->modname,
                'text' => $this->formatString($option->text)
            ];
        }
    }
    /**
     * Getting custom module fields
     * @param $module
     * @param $id
     * @param $contextid
     */
    function getTypeLabelContent(&$module, $id, $contextid)
    {
        unset($module->name);
    }

    /**
     * Getting custom module fields
     * @param $module
     * @param $id
     * @param $contextid
     */
    function getTypeQuizContent(&$module, $id, $contextid)
    {
        $quiz = $this->instances[$module->modname][$this->cms[$id]->instance];

        foreach ($quiz->questions as $key => $question) {
            $quiz->questions[$key]->name = $this->formatText($quiz->questions[$key]->name, $contextid);
            $quiz->questions[$key]->questiontext = $this->formatText($quiz->questions[$key]->questiontext, $contextid);
            $quiz->questions[$key]->generalfeedback = $this->formatText($quiz->questions[$key]->generalfeedback, $contextid);
            foreach ($question->params as &$param) {
                foreach ($param as $paramname => &$paramvalue) {
                    if (!in_array($paramname, ['instance', 'modname'])) {
                        $paramvalue = $this->formatText($paramvalue, $contextid);
                    }
                }
                unset($paramvalue);
            }
            unset($param);
            foreach ($question->hints as &$hint) {
                foreach ($hint as $hintname => &$hintvalue) {
                    if (!in_array($hintname, ['instance', 'modname'])) {
                        $hintvalue = $this->formatText($hintvalue, $contextid);
                    }
                }
                unset($hintvalue);
            }
            unset($hint);
        }
        $module->questions = $quiz->questions;
    }

    /**
     * Getting custom module fields
     * @param $module
     * @param $id
     * @param $contextid
     */
    function getTypePageContent(&$module, $id, $contextid)
    {
        $module->content = $this->formatText($this->instances[$module->modname][$this->cms[$id]->instance]->content, $contextid);
    }

    /**
     * Text formatting
     *
     * @param $text
     * @param null $contextid
     * @return array|string
     */
    function formatText($text, $contextid = null)
    {
        $formattedtext = '';
        if (is_array($this->contentlangs)) {
            $formattedtext = [];
            foreach ($this->contentlangs as $key => $value) {
                force_current_language($key);
                $text = $this->formatLink($text, $contextid);
                $formattedtext[$key] = format_text($text, FORMAT_HTML, ['noclean' => true]);
            }

        } else {
            force_current_language($this->contentlangs);
            $text = $this->formatLink($text, $contextid);
            $formattedtext = format_text($text, FORMAT_HTML, ['noclean' => true]);
        }

        force_current_language('en');
        return $formattedtext;
    }

    function getTokenForImage()
    {
        global $DB;
        $obj = get_config('local_translate', 'token_for_image');
        $result = json_decode($obj);
        $update = false;
        if (empty($result)) {
            $update = true;
        } else {
            if (($result->time + (60 * 60)) < time()) {
                $update = true;
            } elseif (!$DB->get_record_sql("SELECT et.token
                        FROM {external_tokens} et
                        WHERE ((et.validuntil > UNIX_TIMESTAMP()) or (et.validuntil = 0)) and et.token = :token ", ['token' => $result->token])) {
                $update = true;
            }

        }
        if ($update) {
            $result = $DB->get_record_sql("SELECT et.token,if(et.validuntil <> 0,et.validuntil-UNIX_TIMESTAMP(),9999999999) AS sort
            FROM {external_tokens} et
            JOIN {external_services_functions} esf ON esf.externalserviceid = et.externalserviceid AND esf.functionname = 'local_translate_get_image_by_name'
            WHERE ((et.validuntil > UNIX_TIMESTAMP()) or (et.validuntil = 0))
            ORDER BY sort DESC
            LIMIT 1");
            if (!$result){
                return false;
            }
            set_config('token_for_image', json_encode(['token' => $result->token, 'time' => time()]), 'local_translate');
        }
        return $result->token;
    }
    /**
     * Replacing image links to external links
     *
     *
     *
     * @param $text
     * @param $contextid
     * @param bool $revert
     * @return string|string[]|null
     */
    function formatLink($text, $contextid, $revert = false)
    {
        global $CFG;
        $params['wstoken'] = self::getTokenForImage();
        if (empty($params['wstoken'])){
            return $text;
        }
        $params['wsfunction'] = 'local_translate_get_image_by_name';
        $params['moodlewsrestformat'] = 'json';

        if (!$revert) {
            $p = '/(<img[^.*]*src=")(\@\@PLUGINFILE\@\@\/([^"]*))(")/m';
            $text = preg_replace_callback($p, function ($matches) use ($contextid, $params) {
                global $DB;
                $params['name'] = $DB->get_field('files', 'pathnamehash', ['contextid' => $contextid, 'filename' => urldecode($matches[3])], IGNORE_MULTIPLE);
                if (empty($params['name'])) {
                    $params['name'] = $DB->get_field('files', 'pathnamehash', ['contextid' => 1, 'filename' => urldecode($matches[3])], IGNORE_MULTIPLE);
                    if (empty($params['name'])) {
                        return "{$matches[0]}";
                    }
                }
                $link = new \moodle_url('/webservice/rest/server.php', $params);
                return "{$matches[1]}{$link->out(false)}{$matches[4]} moodle-link=\"{$matches[2]}\"";

            }, $text);
        } else {
            $p = "/(<img[^.*]*src=\")(?:[^\s*]*)webservice\/rest\/server\.php\?wstoken=(?:[^.*]*)[&|&amp;]wsfunction={$params['wsfunction']}[&|&amp;]moodlewsrestformat={$params['moodlewsrestformat']}[&|&amp;]name=([^\"]*)(\")/mx";
            $text = preg_replace_callback($p, function ($matches) {
                global $DB;
                $filename = $DB->get_field('files', 'filename', ['pathnamehash' => urldecode($matches[2])]);
                return "{$matches[1]}@@PLUGINFILE@@/{$filename}{$matches[3]}";

            }, $text);
        }
        return $text;
    }

    /**
     * String formatting
     *
     * @param $text
     * @return string
     */
    function formatString($text)
    {
        $formattedtext = '';
        if (is_array($this->contentlangs)) {
            $formattedtext = [];
            foreach ($this->contentlangs as $key => $value) {
                force_current_language($key);
                $formattedtext[$key] = format_string($text);
            }

        } else {
            force_current_language($this->contentlangs);
            $formattedtext = format_string($text);
        }

        force_current_language('en');
        return $formattedtext;

    }

    /**
     * Load all course data to class
     *
     * @param $id
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    protected function loadCourse($id)
    {
        global $DB;
        $this->course = get_course($id);
        if ($this->course) {
            $this->modinfo = get_fast_modinfo($id);
            $this->sections = $this->modinfo->get_section_info_all();
            $this->cms = $this->modinfo->get_cms();
            $this->coursesections = $this->modinfo->get_sections();
            foreach ($this->modinfo->instances as $key => $instance) {
                $this->instances[$key] = $DB->get_records($key, ['course' => $id]);
                if ($key == 'quiz') {
                    $this->loadQuestion();
                } elseif ($key == 'choice'){
                    $this ->loadChoiceOptions();
                } elseif ($key == 'feedback'){
                    $this ->loadFeedbackOptions();
                } elseif ($key == 'glossary'){
                    $this ->loadGlossaryEntries();
                } elseif ($key == 'lesson'){
                    $this ->loadLessonPages();
                } elseif ($key == 'book'){
                    $this ->loadBookCharters();
                } elseif ($key == 'wiki'){
                    $this ->loadWikinPages();
                } elseif ($key == 'workshop'){
                    $this ->loadWorkshopPages();
                }
            }
        }
    }

    /**
     * Getting additional fields for modules
     *
     * @throws \dml_exception
     */
    function loadWorkshopPages(){
        global $DB;
        foreach ($this->instances['workshop'] as $workshop) {
            $accumulative = $DB->get_records_select('workshopform_accumulative', 'workshopid = :workshopid', ['workshopid' => $workshop->id], '', "id AS 'instance','workshopform_accumulative' AS 'modname', description");
            if (!empty($accumulative)) {
                $accumulative = array_values($accumulative);
                $this->instances["workshop"][$workshop->id]->accumulative = $accumulative;
            }

            $rubric = $DB->get_records_select('workshopform_rubric', 'workshopid = :workshopid', ['workshopid' => $workshop->id], '', "id AS 'instance','workshopform_rubric' AS 'modname', description");
            if (!empty($rubric)) {
                $rubric = array_values($rubric);
                $this->instances["workshop"][$workshop->id]->rubric = $rubric;
            }
        }
    }
    /**
     * Getting additional fields for modules
     *
     * @throws \dml_exception
     */
    function loadWikinPages(){
        global $DB;
        foreach ($this->instances['wiki'] as $wiki) {
            $pages = $DB->get_records_sql("SELECT wp.id AS 'instance','wiki_pages' AS 'modname', wp.title, wp.cachedcontent
                FROM {wiki_pages} wp
                JOIN {wiki_subwikis} ws ON wp.subwikiid = ws.id AND ws.wikiid = :wikiid",['wikiid' => $wiki->id]);
            if (!empty($pages)) {
                $pages = array_values($pages);
                $this->instances["wiki"][$wiki->id]->pages = $pages;
            }
        }
    }
    /**
     * Getting additional fields for modules
     *
     * @throws \dml_exception
     */
    function loadBookCharters(){
        global $DB;
        foreach ($this->instances['book'] as $book) {
            $charters = $DB->get_records_select('book_chapters', 'bookid = :bookid', ['bookid' => $book->id], '', "id AS 'instance','book_chapters' AS 'modname', title, content");
            if (!empty($charters)) {
                $charters = array_values($charters);
                $this->instances["book"][$book->id]->charters = $charters;
            }
        }
    }
    /**
     * Getting additional fields for modules
     *
     * @throws \dml_exception
     */
    function loadLessonPages(){
        global $DB;
        foreach ($this->instances['lesson'] as $lesson) {
            $pages = $DB->get_records_select('lesson_pages', 'lessonid = :lessonid', ['lessonid' => $lesson->id], '', "id AS 'instance','lesson_pages' AS 'modname', title, contents");
            if (!empty($pages)) {
                $pages = array_values($pages);
                $this->instances["lesson"][$lesson->id]->pages = $pages;
            }
            $answers = $DB->get_records_select('lesson_answers', 'lessonid = :lessonid', ['lessonid' => $lesson->id], '', "id AS 'instance','lesson_answers' AS 'modname', answer, response");
            if (!empty($answers)) {
                $answers = array_values($answers);
                $this->instances["lesson"][$lesson->id]->answers = $answers;
            }
        }
    }
    /**
     * Getting additional fields for modules
     *
     * @throws \dml_exception
     */
    function loadGlossaryEntries(){
        global $DB;
        foreach ($this->instances['glossary'] as $glossary) {
            $entries = $DB->get_records_select('glossary_entries', 'glossaryid = :glossaryid', ['glossaryid' => $glossary->id], '', "id AS 'instance','glossary_entries' AS 'modname',concept,definition");
            if (!empty($entries)) {
                $entries = array_values($entries);
                $this->instances["glossary"][$glossary->id]->entries = $entries;
            }
        }
    }

    /**
     * Getting additional fields for modules
     */
    function loadFeedbackOptions(){
        global $DB;
    }
    /**
     * Getting additional fields for modules
     *
     * @throws \dml_exception
     */
    function loadChoiceOptions(){
        global $DB;
        foreach ($this->instances['choice'] as $choice) {
            $options = $DB->get_records_select('choice_options', 'choiceid = :choiceid', ['choiceid' => $choice->id], '', "id AS 'instance','choice_options' AS 'modname',text AS text");
            if (!empty($options)) {
                $options = array_values($options);
                $this->instances["choice"][$choice->id]->options = $options;
            }
        }
    }


    /**
     * Load all question and answer data to class
     *
     * @throws \dml_exception
     */
    function loadQuestion()
    {
        global $DB;
        foreach ($this->instances['quiz'] as $quiz) {
            $DB->execute("SET SESSION group_concat_max_len = 1000000");
            $questions = $DB->get_records_sql('select q.*
                            from {quiz_slots} qs
                            join {question} q on q.id = qs.questionid
                            where qs.quizid = :quizid and q.hidden = :hidden', ['quizid' => $quiz->id, 'hidden' => 0]);
            $this->instances["quiz"][$quiz->id]->questions = [];

            foreach ($questions as $question_item) {
                $question = new \stdClass();
                $question->modname = 'question';
                $question->instance = $question_item->id;
                $question->name = $question_item->name;
                $question->questiontext = $question_item->questiontext;
                $question->generalfeedback = $question_item->generalfeedback;

                $qtype = $question_item->qtype;
                $method = "loadQuestionType{$qtype}";
                $question->params = [];
                if (method_exists($this, $method)) {
                    $this->$method($question,$question_item->id);
                }
                $this->loadQuestionTypeHints($question,$question_item->id);

                $this->instances["quiz"][$quiz->id]->questions[] = $question;
            }
        }
    }

    /**
     * Getting hints
     * @param $module
     * @param $id
     * @throws \dml_exception
     */
    function loadQuestionTypeHints(&$module,$id){
        global $DB;
        $hints = $DB->get_records_select('question_hints', 'questionid = :questionid', ['questionid' => $id], '', "id as instance,'question_hints' as modname, hint");
        $module->hints = array_values($hints);
    }

    /**
     * Question Type Ddmarker
     * @param $module
     * @param $id
     * @throws \dml_exception
     */
    function loadQuestionTypeDdmarker(&$module,$id){
        global $DB;
        $ddmarkers = $DB->get_records_select('qtype_ddmarker', 'questionid = :questionid', ['questionid' => $id], '', "id as instance,'qtype_ddmarker' as modname,correctfeedback,partiallycorrectfeedback,incorrectfeedback");
        $module->params = array_merge($module->params,array_values($ddmarkers));
    }

    /**
     * Question Type Essay
     *
     * @param $module
     * @param $id
     * @throws \dml_exception
     */
    function loadQuestionTypeEssay(&$module,$id){
        global $DB;
        $answers = $DB->get_records_select('qtype_essay_options', 'questionid = :questionid', ['questionid' => $id], '', "id as instance,'qtype_essay_options' as modname,graderinfo");
        $module->params = array_merge($module->params,array_values($answers));

    }

    /**
     * Question Type Match
     *
     * @param $module
     * @param $id
     * @throws \dml_exception
     */
    function loadQuestionTypeMatch(&$module,$id){
        global $DB;
        $answers = $DB->get_records_select('qtype_match_options', 'questionid = :questionid', ['questionid' => $id], '', "id as instance,'qtype_match_options' as modname,correctfeedback,partiallycorrectfeedback,incorrectfeedback");
        $module->params = array_merge($module->params,array_values($answers));
        $answers = $DB->get_records_select('qtype_match_subquestions', 'questionid = :questionid', ['questionid' => $id], '', "id as instance,'qtype_match_subquestions' as modname,questiontext,answertext");
        $module->params = array_merge($module->params,array_values($answers));
    }

    /**
     * Question Type Multichoice
     *
     * @param $module
     * @param $id
     * @throws \dml_exception
     */
    function loadQuestionTypeMultichoice(&$module,$id){
        global $DB;
        $multichoices = $DB->get_records_select('qtype_multichoice_options', 'questionid = :questionid', ['questionid' => $id], '', "id as instance,'qtype_multichoice_options' as modname,correctfeedback,partiallycorrectfeedback,incorrectfeedback");
        $module->params = array_merge($module->params,array_values($multichoices));
    }

    /**
     * Setting course content
     *
     * @param $newcourse
     */
    function set_content($newcourse)
    {
        $newcourse['instance'] = $newcourse['id'];
        $newcourse['modname'] = 'course';

        $this->update_record_r($newcourse, $this->backup);

    }

    /**
     * Recursive updating of fields in the database
     *
     * @param $instance
     * @param $backup
     */
    function update_record_r($instance, $backup)
    {
        try {
            $instanceid = $instance['instance'];
            $modname = $instance['modname'];
            $update = new \stdClass();
            $update->id = $instance['instance'];
            foreach ($instance as $key => $value) {
                if (in_array($key, $this->_ignorefiedls)) {
                    continue;
                }
                if (is_array($value)) {
                    if (isset($value['en']) || isset($value['ru'])) {
                        $update->$key = $this->setProperty($modname, $instanceid, $key, $value);
                    }
                    if (isset($value[0]) && isset($value[0]['instance'])) {
                        foreach ($value as $temp_key => $item) {
                            $this->update_record_r($item, $backup[$key][$temp_key]);
                        }
                    }
                }
            }
            global $DB;
            if (!$DB->update_record($modname, $update)) {
                $this->response['error']["{$modname}_{$instanceid}"] = false;
            } else {
                $this->response['updated']["{$modname}_{$instanceid}"] = true;
            }
        } catch (\Exception $e) {
            $this->response['error']["{$modname}_{$instanceid}"] = $e->getMessage();
        }
    }

    /**
     * Setting the property
     *
     * @param $modname
     * @param $instanceid
     * @param $key
     * @param $value
     * @return string
     */
    function setProperty($modname, $instanceid, $key, $value)
    {
        $translatedtext = '';
        if (!in_array($key, $this->_ignorefiedls) && is_array($value)) {
            foreach ($this->langs as $langkey => $langvalue) {
                if ($langkey == 'en') {
                    $translate = $this->setPropertyLangEn($modname, $instanceid, $key, $value);
                } else {
                    $value[$langkey] = $this->formatLink($value[$langkey], null, true);
                    $translate = $value[$langkey];
                }
                $translatedtext .= "{mlang {$langkey}}" . $translate . "{mlang}";
            }
            return $translatedtext;
        }
        return $translatedtext;
    }

    /**
     * @param $modname
     * @param $instanceid
     * @param $key
     * @param $value
     * @param $backup
     * @return string|string[]|null
     */
    function setPropertyLangEn($modname, $instanceid, $key, $value){
        if (isset($value['en'])) {
            $translate = $this->formatLink($value['en'], null, true);
        } else {
            global $DB;
            $translate = $DB->get_field($modname, $key, ['id' => $instanceid]);
            $translate = $formattedtext[$key] = format_text($translate, FORMAT_HTML, ['noclean' => true]);
            $translate = $formattedtext[$key] = $this->formatLink($translate, null, true);
        }
        return $translate;
    }

}