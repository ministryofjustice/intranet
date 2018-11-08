<?php

namespace MOJ_Intranet\List_Tables;

abstract class List_Table {
    /**
     * Array of object types which this class applies to.
     *
     * Custom post types must be prepended with "_posts"
     *   e.g. "users", "pages", "webchat_posts"
     *
     * @var array
     */
    protected $object_types = array();

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
    public function __construct() {
        $this->hook_wordpress_filters();
    }

    /**
     * Hook WordPress filters to object methods.
     */
    public function hook_wordpress_filters() {
        foreach ($this->object_types as $object_type) {
            // Add columns to the table
            add_filter('manage_' . $object_type . '_columns', array($this, 'filter_columns'));

            // Note: Conditional statement required here due to an inconsistency in WordPress!
            if ($object_type == 'users') {
                // For some reason the 'manage_user_custom_column' is actually a filter, so must behave differently.
                add_action('manage_' . $object_type . '_custom_column', array($this, 'filter_custom_column'), 10, 3);
            } else {
                // For every other object type we need to hook into an action.
                add_action('manage_' . $object_type . '_custom_column', array($this, 'action_custom_column'), 10, 2);
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
    public function filter_columns($columns) {
        return array_merge($columns, $this->columns);
    }

    public function filter_custom_column($value, $column_id, $object_id) {
        if (isset($this->columns[$column_id])) {
            $method = 'column_' . $this->sanitize_column_id($column_id);
            $value = $this->$method($object_id);
        }
        return $value;
    }

    public function action_custom_column($column_id, $object_id) {
        if (isset($this->columns[$column_id])) {
            $method = 'column_' . $this->sanitize_column_id($column_id);
            echo $this->$method($object_id);
        }
    }

    /**
     * Sanitize a column_id (e.g. "column-name") to be used in a method name.
     *
     * @param string $string
     * @return string
     */
    public function sanitize_column_id($string) {
        $sanitized = sanitize_title($string);
        $sanitized = str_replace('-', '_', $sanitized);
        return $sanitized;
    }
}
