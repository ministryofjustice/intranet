<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

/**
 * QueryProps
 * 
 * This class is responsible for handling the query properties.
 * It is used for backend and AJAX requests, to get consistent results.
 * 
 * @package Clarity
 *
 * @property string $agency - The active agency.
 * @property string $post_type - The post type.
 * @property int $page - The page number.
 * @property int $posts_per_page - The number of posts per page.
 * @property ?bool $exclude_current - Exclude the current post.
 * @property ?string $keywords_filter - The keywords filter.
 * @property ?string $date_filter - The date filter.
 * @property ?string $news_category_id - The news category ID.
 * @property ?string $region_id - The region ID.
 *
 * @return void
 */

class SearchQueryArgs
{
    public function __construct(
        public string $agency_term_id,
        public string $post_type,
        public int $page,
        public int $posts_per_page = 10,
        public ?bool $exclude_current = false,
        public ?string $keywords_filter = null,
        public ?string $date_filter = null,
        public ?string $news_category_id = null,
        public ?string $region_id = null,
    ) {}

    function get()
    {
        // Pagination.
        $offset = $this->page ? (($this->page - 1) * $this->posts_per_page) : 0;

        $args = [
            'posts_per_page' => $this->posts_per_page,
            'post_type' => $this->post_type,
            'post_status' => 'publish',
            'offset' => $offset,
            ...($this->exclude_current ? ['post__not_in' => [get_the_ID()]] : []),
            'tax_query' => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'agency',
                    'field' => 'term_id',
                    'terms' => $this->agency_term_id
                ],
                // If the region is set add its ID to the taxonomy query
                ...(!empty($this->region_id) ? [
                    'taxonomy' => 'region',
                    'field' => 'region_id',
                    'terms' =>  $this->region_id,
                ] : []),
                // If the news category is set add its ID unless the query is regional, 
                // as it will have already been added to the tax query.
                ...(!empty($this->news_category_id) && empty($this->region_id) ? [
                    'taxonomy' => 'news_category',
                    'field' => 'category_id',
                    'terms' =>  $this->news_category_id,
                ] : []),
            ]
        ];

        // Parse dates from the date filter.
        if (!empty($this->date_filter)) {
            preg_match('/&after=([^&]*)&before=([^&]*)/', $this->date_filter, $matches);
            $args['date_query'] = [
                'after' =>  date('Y-m-d', strtotime($matches[1])),
                'before' => date('Y-m-d', strtotime($matches[2])),
                'inclusive' => false,
            ];
        }

        // If there is a search query, set the orderby to relevance.
        if (!empty($this->keywords_filter)) {
            $args['orderby'] = 'relevance';
            $args['s'] = $this->keywords_filter;
        }

        return $args;
    }
}