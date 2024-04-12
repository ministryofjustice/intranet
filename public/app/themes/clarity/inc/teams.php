<?php
namespace MOJ\Intranet;

/**
 * Retrieves and returns guidance and form related data
 */

class Teams
{

    private $page_meta;

    public function __construct()
    {
        $this->page_meta = [
            'post_id'  => get_the_ID(),
            'agency'   => get_intranet_code(),
            'home_url' => get_home_url(),
        ];
    }

     /**
      *
      * Team News API
      *
      * @param
      * @return
      */
    public function team_news_api($number)
    {
        $oAgency = new Agency();
        $activeAgency = $oAgency->getCurrentAgency();

        $post_per_page = 10;

        $args = [
            'numberposts' => $post_per_page,
            'post_type' => 'team-news',
            'post_status' => 'publish',
            'tax_query' => [
              'relation' => 'AND',
              [
                'taxonomy' => 'agency',
                'field' => 'term_id',
                'terms' => $activeAgency['wp_tag_id']
              ],
          ]
        ];

        $posts = get_posts($args);
        return $posts;
    }

     /**
      *
      * Team Blog API
      *
      * @param
      * @return
      */
    public function team_blog_api($number)
    {
        $oAgency = new Agency();
        $activeAgency = $oAgency->getCurrentAgency();

        $post_per_page = 10;

        $args = [
            'numberposts' => $post_per_page,
            'post_type' => 'team-blogs',
            'post_status' => 'publish',
            'tax_query' => [
              'relation' => 'AND',
              [
                'taxonomy' => 'agency',
                'field' => 'term_id',
                'terms' => $activeAgency['wp_tag_id']
              ],
          ]
        ];

        $posts = get_posts($args);
        return $posts;
    }

     /**
      *
      * Team Events API
      *
      * @param
      * @return
      */
    public function team_events_api($number)
    {
        $oAgency = new Agency();
        $activeAgency = $oAgency->getCurrentAgency();

        $post_per_page = 10;

        $args = [
            'numberposts' => $post_per_page,
            'post_type' => 'team-events',
            'post_status' => 'publish',
            'tax_query' => [
              'relation' => 'AND',
              [
                'taxonomy' => 'agency',
                'field' => 'term_id',
                'terms' => $activeAgency['wp_tag_id']
              ],
          ]
        ];

        $posts = get_posts($args);
        return $posts;
    }
}
