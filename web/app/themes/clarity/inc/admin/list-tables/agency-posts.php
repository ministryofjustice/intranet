<?php

namespace MOJ_Intranet\List_Tables;

use Agency_Editor;
use Agency_Context;
use Region_Context;

/**
 * Adjustments to list tables for all post types which have agency taxonomy.
 */

class Agency_Posts extends List_Table
{
    /**
     * Array of object types which this class applies to.
     *
     * Post types must be prepended with "_posts", but not objects.
     *   e.g. "users", "post_posts" "page_posts"
     *
     * @var array
     */
    protected $object_types = array(
        'news_posts',
        'post_posts',
        'page_posts',
        'webchat_posts',
        'event_posts',
        'document_posts',
        'snippet_posts',
    );

    /**
     * Array of columns to add to the table.
     * To reorder them, extend the filterColumns() method.
     *
     * @var array
     */
    public $columns = [];

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Reorder columns.
     *
     * @param array $columns
     * @return array
     */
    public function filter_columns($columns)
    {
        // Reorder columns to be: checkbox, title, $this->columns, then everything else.
        $columns = array_merge(
            array_intersect_key($columns, array_flip(array('cb', 'title'))),
            $this->columns,
            $columns
        );

        return $columns;
    }
}
