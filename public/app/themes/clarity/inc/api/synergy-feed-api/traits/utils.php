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
    public static function remove_class_object_filter(string $filter_name, string $class_name, string $function_name): void
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

    /**
     * Validate date-time string, in ISO format.
     * 
     * This function checks if the provided date-time string is in ISO 8601 format.
     * It returns true if the string is valid, otherwise false.
     * 
     * @param string|null $date_time The date-time string to validate.
     * @return bool True if the date-time string is valid, false otherwise.
     */

    public static function isValidIsoDateTime($date_time = null): bool
    {
        if (empty($date_time) || gettype($date_time) !== 'string') {
            // If the date_time is empty or not a string, return false.
            return false; 
        }

        // Example value: modified_after=2024-05-01T00:00:00
        // Example value: modified_after=2024-05-01T00:00:00Z
        // Example value: modified_after=2024-05-01T08:03:00%2b01:00
        // Example value: modified_after=2024-05-01T08:03:00-01:00

        $iso_pattern = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[\+\-]\d{2}:\d{2})?$/';

        return (bool) preg_match($iso_pattern, $date_time);
    }


    /**
     * Find an entry in an array that matches a filter function.
     *
     * @param array $array The array to search.
     * @param callable $filter_function The filter function to use.
     * @return mixed|null The first entry in the array that matches the filter function.
     */

    public static function arrayFind(array $array, callable $filter_function): mixed
    {
        foreach ($array as $entry) {
            if (call_user_func($filter_function, $entry) === true) {
                return $entry;
            }
        }
        return null;
    }
}
