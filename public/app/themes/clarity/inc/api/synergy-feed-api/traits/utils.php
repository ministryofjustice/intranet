<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

trait Utils
{
    /**
     * Remove a filter that is added by an object method.
     * 
     * This function can be used to remove a filter, when otherwise it would be impossible to remove it.
     * @see https://stackoverflow.com/a/43942428/6671505
     * 
     * @param string $filter_name The name of the filter to remove.
     * @param string $class_name The name of the class that the method belongs to.
     * @param string $function_name The name of the method to remove.
     * @return void
     */
    public function remove_class_object_filter(string $filter_name, string $class_name, string $function_name): void
    {
        global $wp_filter;
        foreach ($wp_filter[$filter_name]->callbacks as $priority => $pri_data) {
            foreach ($pri_data as $cb => $cb_data) {
                // If the function is not an array, then skip it.
                if (!is_array($cb_data['function'])) continue;
                
                // If function index 1 is not the function name, then skip it.
                if ($cb_data['function'][1] !== $function_name) continue;
                
                // The passed in $function name could match in 2 ways:
                // 1. The class name matches the class of the object.
                $class_match = is_object($cb_data['function'][0]) && get_class($cb_data['function'][0]) == $class_name;
                // 2. The function name matches for example a static class.
                $static_class_match = $cb_data['function'][0] === $class_name;
                // If neither match, then skip it.
                if (!$class_match && !$static_class_match) continue;
                
                // If we have a match, then remove the filter.
                remove_filter($filter_name,  [$cb_data['function'][0], $function_name], $priority);

                // Break out of both foreach loops.
                break 2;
            }
        }
    }
}
