<?php

namespace MOJ_Intranet\List_Tables;

use Agency_Editor;

/**
 * Adjustments to list tables for all post types which have agency taxonomy.
 */

class Agency_Posts extends List_Table {
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
    public $columns = array(
        'opted-out' => 'Opt-Out Status',
        'agency' => 'Agency',
    );

    /**
     * Reorder columns.
     *
     * @param array $columns
     * @return array
     */
    public function filter_columns($columns) {
        // Reorder columns to be: checkbox, title, $this->columns, then everything else.
        $columns = array_merge(
            array_intersect_key($columns, array_flip(array('cb', 'title'))),
            $this->columns,
            $columns
        );

        // Remove the default taxonomy 'Agencies' column
        if (isset($columns['taxonomy-agency'])) {
            unset($columns['taxonomy-agency']);
        }

        // Don't show the 'opted out' column if the user is not an Agency Editor
        if (!current_user_can('agency-editor')) {
            unset($columns['opted-out']);
        }

        return $columns;
    }

    /**
     * Content for the Agency column.
     *
     * @param int $post_id
     * @return string
     */
    public function column_agency($post_id) {
        $agency = Agency_Editor::get_post_agency($post_id);
        $agency = Agency_Editor::get_agency_by_slug($agency);
        return $agency->name;
    }

    /**
     * Content for the Opted Out column.
     *
     * @param $post_id
     * @return string
     */
    public function column_opted_out($post_id) {
        if (!current_user_can('agency-editor')) {
            return '–';
        }

        $opt_out = Agency_Editor::is_post_opted_out($post_id);

        if (is_null($opt_out)) {
            $out = '';
        } elseif ($opt_out === true) {
            $out = '<span class="dashicons dashicons-hidden"></span>';
        } else {
            $out = '<span class="dashicons dashicons-visibility"></span>';
        }

        return $out;
    }
}
