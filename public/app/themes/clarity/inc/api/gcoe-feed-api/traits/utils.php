<?php

namespace MOJ\Intranet\GcoeFeedApi;

defined('ABSPATH') || exit;

trait Utils
{
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
