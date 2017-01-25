<?php if (!defined('ABSPATH')) die();

class Featured_model extends MVC_model {
  public function __construct() {
    parent::__construct();

    $this->max_featured_items = 2;
  }

  public function get_list($options = array()) {
    $options['post__in'] = $this->get_featured_ids($options['agency']);

    $options = $this->normalize_featured_options($options);

    $args = array (
      // Paging
      'nopaging' => false,
      'offset' => 0,
      'posts_per_page' => $this->max_featured_items,
      // Filters
      'post_type' => ['news', 'post', 'page'],
      'post__in' => $options['post__in'],
      'tax_query' => $options['tax_query'],
      'orderby' => 'post__in'
    );

    $data['raw'] = new WP_Query($args);
    $data['total_results'] = (int) $data['raw']->found_posts;
    $data['retrieved_results'] = (int) $data['raw']->post_count;

    $data = $this->format_data($data, true);

    return $data;
  }

  private function get_featured_ids($agency) {
    $need_to_know_news_ids = array();

    for($a = 1; $a <= $this->max_featured_items; $a++) {
      array_push($need_to_know_news_ids, get_option($agency . '_featured_story' . $a));
    }

    return $need_to_know_news_ids;
  }

  private function normalize_featured_options($options) {
    $default = array(
      'start' => 1,
      'length' => 10,
      'post_type' => 'news'
    );

    foreach($options as $key=>$value) {
      if($value) {
        $default[$key] = $value;
      }
    }

    return $default;
  }

  /** Format and trim the raw results
   * @param {Object} $data Raw results object
   * @return {Array} Formatted results
   */
  private function format_data($data, $featured = false) {
    $data['results'] = array();

    foreach($data['raw']->posts as $post) {
      $data['results'][] = $this->format_row($post, $featured);
    }

    unset($data['raw']);

    return $data;
  }

  /** Format a single results row
   * @param {Object} $post Post object
   * @return {Array} Formatted and trimmed post
   */
  private function format_row($post, $featured = false) {
    $id = $post->ID;

    $post_object = get_post($id);

    $thumbnail_type = 'intranet-large';
    $thumbnail_id = get_post_thumbnail_id($id);
    $thumbnail = wp_get_attachment_image_src($thumbnail_id, $thumbnail_type);
    $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
    $authors = dw_get_author_info($id);

    return [
      'id' => $id,
      'post_type' => get_post_type($id),
      'title' => (string) get_the_title($id),
      'url' => (string) get_the_permalink($id),
      'slug' => (string) $post->post_name,
      'excerpt' => (string) $post->post_excerpt,
      'thumbnail_url' => (string) $thumbnail[0],
      'thumbnail_alt_text' => (string) $alt_text,
      'timestamp' => (string) get_the_time('Y-m-d H:i:s', $id),
      'authors' => $authors
    ];
  }
}
