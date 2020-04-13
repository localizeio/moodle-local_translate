<?php
/**
 * Fitlers
 */

namespace local_translate;


use Horde\Socket\Client\Exception;

/**
 * Class filters_controller
 * @package local_translate
 */
class filters_controller
{
    const NAME_TRANSLATE_FILTER = 'multilang2';

    /**
     * Temporary Filter Storage
     *
     * @var
     */
    private $filter_dump;

    /**
     * Disable Filters
     * @return bool
     * @throws \dml_exception
     * @throws \dml_transaction_exception
     */
    public function off(){
        global $DB;
        try {
            $tr = $DB->start_delegated_transaction();
            $filters = $DB->get_records('filter_active', ['active' => 1]);
            $this->filter_dump = $filters;
            foreach ($filters as $filter){
                if($filter->filter != self::NAME_TRANSLATE_FILTER) {
                    $filter->active = -9999;
                    $DB->update_record('filter_active', $filter);
                }
            }
            $tr->allow_commit();
            return true;
        }catch (Exception $e){
            if (!empty($tr)) {
                $tr->rollback($e);
            }
            return false;
        }
    }

    /**
     * Enable Filters
     *
     * @return bool
     * @throws \dml_exception
     * @throws \dml_transaction_exception
     */
    public function on(){
        global $DB;
        if(!empty($this->filter_dump)){
            try{
                $tr = $DB->start_delegated_transaction();
                foreach ($this->filter_dump as $filter){
                    if($filter->filter != self::NAME_TRANSLATE_FILTER) {
                        $filter->active = 1;
                        $DB->update_record('filter_active', $filter);
                    }
                }
                $tr->allow_commit();
                return true;
            }catch (Exception $e){
                if (!empty($tr)) {
                    $tr->rollback($e);
                }
                return false;
            }
        }else{
            return false;
        }
    }
}