<?php

namespace MOJIntranet\ListTables;

use AgencyEditor;

/**
 * Adjustments to list tables for all post types which have agency taxonomy.
 */

class AgencyPosts extends ListTable
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
     * AgencyPosts constructor.
     */
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
    public function filterColumns($columns)
    {
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
     * Content for the Agencies column.
     *
     * @param int $postId
     * @return string
     */
    public function columnAgency($postId)
    {
        $agency = AgencyEditor::getPostAgency($postId);
        return $agency->name;
    }

    /**
     * Content for the Opted Out column.
     *
     * @param $postId
     * @return string
     */
    public function columnOptedOut($postId)
    {
        if (!current_user_can('agency-editor')) {
            return '–';
        }

        $optOut = AgencyEditor::isPostOptedOut($postId);

        if (is_null($optOut)) {
            $out = '';
        } elseif ($optOut === true) {
            $out = '<span class="dashicons dashicons-hidden"></span>';
        } else {
            $out = '<span class="dashicons dashicons-visibility"></span>';
        }

        return $out;
    }
}
