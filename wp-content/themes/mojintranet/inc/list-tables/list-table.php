<?php

namespace MOJIntranet\ListTables;

class ListTable
{
    /**
     * Array of object types which this class applies to.
     *
     * Custom post types must be prepended with "_posts"
     *   e.g. "users", "pages", "webchat_posts"
     *
     * @var array
     */
    protected $objectTypes = array();

    /**
     * Associative array of columns to add to the table.
     * To reorder them, extend the filterColumns() method.
     *   e.g. 'column_id' => 'Column Heading'
     *
     * @var array
     */
    public $columns = array();

    /**
     * ListTable constructor.
     */
    public function __construct()
    {
        $this->hookWordPressFilters();
    }

    /**
     * Hook WordPress filters to object methods.
     */
    public function hookWordPressFilters()
    {
        foreach ($this->objectTypes as $objectType) {
            // Add columns to the table
            add_filter('manage_' . $objectType . '_columns', array($this, 'filterColumns'));

            // Note: Conditional statement required here due to an inconsistency in WordPress!
            if ($objectType == 'users') {
                // For some reason the 'manage_user_custom_column' is actually a filter, so must behave differently.
                add_action('manage_' . $objectType . '_custom_column', array($this, 'filterCustomColumn'), 10, 3);
            } else {
                // For every other object type we need to hook into an action.
                add_action('manage_' . $objectType . '_custom_column', array($this, 'actionCustomColumn'), 10, 2);
            }
        }
    }

    /**
     * Filter the array of column headings for the table.
     * Extend this method if you want to reorder or alter the returned array.
     *
     * @param array $columns
     * @return array
     */
    public function filterColumns($columns)
    {
        return array_merge($columns, $this->columns);
    }

    public function filterCustomColumn($value, $columnName, $objectId)
    {
        if (isset($this->columns[$columnName])) {
            $method = 'column' . $this->toCamelCase($columnName);
            $value = $this->$method($objectId);
        }
        return $value;
    }

    public function actionCustomColumn($columnName, $objectId)
    {
        if (isset($this->columns[$columnName])) {
            $method = 'column' . $this->toCamelCase($columnName);
            echo $this->$method($objectId);
        }
    }

    /**
     * Convert an underscored string to camel case.
     *   e.g. "camel_case" becomes "CamelCase"
     *
     * @param string $string
     * @return string
     */
    public function toCamelCase($string)
    {
        $words = str_replace('_', ' ', $string);
        $words = ucwords($words);
        $camel = str_replace(' ', '', $words);
        return $camel;
    }
}