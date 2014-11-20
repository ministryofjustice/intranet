<?php

/**
 * Processes API requests
 *
 * @author ryanajarrett
 * @since 0.2
 */
class api_request {
    public $results_array = array();
    public static $params;
    protected $data;

    /**
     *
     * Sets variables based on $params and query_vars
     *
     */
    function set_params() {
        $i = 1;
        foreach ($this::$params as $param) {
            $this->data[$param] = get_query_var('param' . $i) ? get_query_var('param' . $i) : null;
            $i++;
        }
    }

}