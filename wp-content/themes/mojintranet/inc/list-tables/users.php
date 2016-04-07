<?php

namespace MOJIntranet\ListTables;

/**
 * Adjustments to users list table.
 */

class Users extends ListTable
{
    /**
     * Array of object types which this class applies to.
     *
     * Post types must be prepended with "_posts", but not objects.
     *   e.g. "users", "post_posts" "page_posts"
     *
     * @var array
     */
    protected $objectTypes = array(
        'users',
    );

    /**
     * Array of columns to add to the table.
     * To reorder them, extend the filterColumns() method.
     *
     * @var array
     */
    public $columns = array(
        'agencies' => 'Agencies',
    );

    /**
     * Reorder columns.
     *
     * @param array $columns
     * @return array
     */
    public function filterColumns($columns)
    {
        $columns = parent::filterColumns($columns);

        // Shift the 'Posts' column to the right so that
        // 'Role' and 'Agency' are next to each other.
        $posts = $columns['posts'];
        unset($columns['posts']);
        $columns['posts'] = $posts;

        return $columns;
    }

    /**
     * Return content for the Agencies column.
     *
     * @param int $userId
     * @return string
     */
    public function columnAgencies($userId)
    {
        $terms = wp_get_object_terms($userId, 'agency');
        $user = get_userdata($userId);

        if (!in_array('agency-editor', $user->roles)) {
            // User is not an Agency Editor, so this taxonomy does not apply.
            return '–';
        }

        $agencies = array_map(function($term) {
            return $term->name;
        }, $terms);

        return implode(', ', $agencies);
    }
}
