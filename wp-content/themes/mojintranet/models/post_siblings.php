<?php if (!defined('ABSPATH')) die();

class Post_siblings_model extends MVC_model {

  /** Get a previous and next links for a post object
   * @param {Array} $options Options and filters (see search model for details)
   * @return {Array} Formatted and sanitized results
   */
  public function get_post_sibling_links($options = array()) {
    $this->current_agency = $options['agency'] ?: 'hq';

    if (is_numeric($options['post_id'])) {
      global $post;
      $data = [
          'prev_link' => '',
          'next_link' => '',
      ];

      $post = get_post($options['post_id']);

      if (is_null($post) == false) {
        setup_postdata($post);

        add_filter('get_next_post_join', array($this, 'navigate_in_same_agency_join'), 20);
        add_filter('get_previous_post_join', array($this, 'navigate_in_same_agency_join'), 20);

        add_filter('get_next_post_where' , array($this, 'navigate_in_same_agency_where'));
        add_filter('get_previous_post_where' , array($this, 'navigate_in_same_agency_where'));

        $prev_post = get_previous_post();

        if (is_object($prev_post)) {
          $data['prev_link'] = get_permalink($prev_post->ID);
        }

        $next_post = get_next_post();

        if (is_object($next_post)) {
          $data['next_link'] = get_permalink($next_post->ID);
        }

        wp_reset_postdata();
      }
    }

    return $data;

  }

  public function navigate_in_same_agency_join() {
    global $wpdb;
    return " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
  }

  public function navigate_in_same_agency_where($original) {
    global $wpdb, $post;

    $agency_term = get_term_by('slug', $this->current_agency, 'agency');

    if ( ! $agency_term )
      return $original ;

    $where 	= '';
    $taxonomy = 'agency';
    $op = ('get_previous_post_where' == current_filter()) ? '<' : '>';
    $where = $wpdb->prepare( "AND tt.taxonomy = %s", $taxonomy );

    if ( ! is_object_in_taxonomy( $post->post_type, $taxonomy ) )
      return $original ;

    $where 	= " AND tt.term_id = " . $agency_term->term_id;
    return $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish' $where", $post->post_date, $post->post_type);
  }

}









