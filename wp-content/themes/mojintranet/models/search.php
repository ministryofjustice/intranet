<?php if (!defined('ABSPATH')) die();

class Search_model extends MVC_model {
  private $options = array(); //all search options
  private $meta_query = array(); //stores SQL query parts responsible for checking the existence of meta data
  private $date_query = array();
  private $posts_orderby = '';

  public function __construct() {
    $this->debug = (boolean) $_GET['debug'];
  }

  /** get all posts that meet the criteria from the options
   * @param {Array} $options Search criteria
   *    search_orderby - array of key/value pairs representing fields and sort direction
   *    meta_fields - lists of meta fields used in the sql query
   *    post_type - post type or array of post types
   *    keywords - keywords
   *    page - page number
   *    per_page - how many results per page to show
   *
   * @return Array all matching posts
   */
  public function get($options = array()) {
    $data = $this->get_raw($options);
    $data = $this->format_data($data);
    return $data;
  }

  public function get_raw($options = array()) {
    $data = array();

    $this->options = $this->initialise_options($options);

    //process the query
    $data['raw'] = $this->get_raw_results();
    $data['total_results'] = (int) $data['raw']->found_posts;
    $data['retrieved_results'] = (int) $data['raw']->post_count;

    return $data;
  }

  private function initialise_options($options) {
    $default = array(
      'search_order' => 'ASC',
      'search_orderby' => 'relevance',
      'meta_fields' => array(),
      'page' => 1,
      'per_page' => 10,
      'post_type' => 'all',
      'keywords' => ''
    );

    foreach($options as $key=>$value) {
      if($value) {
        $default[$key] = $value;
      }
    }

    $default['post_type'] = $this->convert_post_type($default['post_type']);

    return $default;
  }

  private function convert_post_type($post_type) {
    switch ($post_type) {
      case 'all':
        $post_type = array('page', 'news', 'document', 'webchat', 'event', 'post');
        break;

      case 'content':
        $post_type = array('page', 'news', 'webchat', 'event', 'post');
        break;
    }

    return $post_type;
  }

  private function get_raw_results() {
    if($this->options['date']) {
      if(count($this->options['meta_fields'])) {
        $this->build_meta_clause();
        $this->build_date_query();
        $date_query = array();
      }
      else {
        $date_query = $this->parse_date();
      }
    }

		$args = array(
			// Paging
			'nopaging' => false,
			'paged' => $this->options['page'],
			'posts_per_page' => $this->options['per_page'],
			// Sorting
      'order' => $this->options['search_order'],
			'orderby' => $this->options['search_orderby'],
			// Filters
			'post_type' => $this->options['post_type'],
			's' => $this->rawurldecode($this->options['keywords']),
			'meta_query' => $this->meta_query,
			'date_query' => $date_query
		);

    add_filter('posts_orderby', array($this, 'wp_filter_posts_orderby'));
    $results = new WP_Query($args);
    remove_filter('posts_orderby', array($this, 'wp_filter_posts_orderby'));

    if (function_exists(relevanssi_do_query) && $this->options['keywords'] != null) {
      relevanssi_do_query($results);
    }

    if($this->debug) {
      Debug::full($results->query, 8);
    }

    return $results;
  }

  private function format_data($data) {
    $data['results'] = array();

    foreach($data['raw']->posts as $post) {
      $data['results'][] = $this->format_row($post);
    }

    unset($data['raw']);

    return $data;
  }

  private function format_row($post) {
    $id = $post->ID;

    $titles = array();
    $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'thumbnail');
    $page_ancestors = get_post_ancestors($id);
    $excerpt = $post->post_type == 'document' ? '' : $post->post_excerpt;

    if (count($page_ancestors)) {
      $titles = array_slice($page_ancestors, 0, 2);
      foreach($titles as $index => $ancestor_id) {
        $titles[$index] = get_post_meta($ancestor_id, 'nav_label', true) ?: get_the_title($ancestor_id);
      }
      $titles = array_reverse($titles);
    }
    else {
      $titles[] = ucfirst($post->post_type);
    }

    return array(
      'id' => $id,
      'title' => (string) get_the_title($id),
      'url' => (string) get_the_permalink($id),
      'slug' => (string) $post->post_name,
      'excerpt' => (string) $excerpt,
      'thumbnail_url' => (string) $thumbnail[0],
      'timestamp' => (string) get_the_time('Y-m-d H:i:s', $id),
      'file_url' => (string) '',
      'file_name' => (string) '',
      'file_size' => (int) 0,
      'file_pages' => (int) 0,
      'content_type' => $titles,
      'relevance' => $post->relevance_score
    );
  }

  private function build_meta_clause() {
		if(count($this->options['meta_fields'])) {
			//foreach ($this->options['meta_fields'] as $meta_field) {
        //$this->meta_query[$meta_field] = array(
          //'key' => $meta_field,
          //'compare' => 'EXISTS'
        //);
			//}

      $mt_count = 0;
      $posts_orderby_parts = array();

      foreach ($this->options['search_orderby'] as $field => $order) {
        if (in_array($field, $this->options['meta_fields'])) {
          $mt_count++;
          $posts_orderby_parts[] = "mt" . $mt_count . ".meta_value" . " " . $order;
        } else {
          $posts_orderby_parts[] = "'" . $field . "'" . " " . $order;
        }
      }

      if($mt_count) {
        $this->posts_orderby = implode(', ', $posts_orderby_parts);
      }
		}
  }

  private function parse_date() {
    if(!$this->options['date']) return;

    $parts = explode('-', $this->options['date']);

    $date['year'] = (int) $parts[0] ?: null;
    $date['monthnum'] = (int) $parts[1] ?: null;
    $date['day'] = (int) $parts[2] ?: null;

    return $date;
  }

  private function build_date_query() {
    $meta_query_or = array('relation' => 'OR');
    $meta_query_and = array('relation' => 'AND');

    $compare = $this->options['date'] ? 'LIKE' : '>=';
    if (is_array($this->options['date'])) {
      //to be checked when rewriting the months API
      $compare = 'BETWEEN';
      if ($this->options['date'][0] == 'today') {
        $compare_value[] = date('Y-m-d');
      } else {
        $compare_value[] = $this->options['date'][0];
      }
      $compare_value[] = date('Y-m-t', strtotime("+" . $this->options['date'][1] . " month"));
    }
    else {
      $compare_value = $this->options['date'] == 'today' ? date('Y-m-d') : $this->options['date'];
    }

    foreach ($this->options['meta_fields'] as $meta_field) {
      $meta_query_or[] = array(
        'key' => $meta_field,
        'value' => $compare_value,
        'type' => 'date',
        'compare' => $compare,
      );
      $meta_query_and[] = array(
        'key' => $meta_field,
      );
    }

    $this->meta_query[] = array($meta_query_or, $meta_query_and);
  }

	private function rawurldecode($string) {
		$string = str_replace('%252F', '%2F', $string);
		$string = str_replace('%255C', '%5C', $string);
		$string = rawurldecode($string);

		return $string;
	}

  function wp_filter_posts_orderby($orderby) {
    if ($this->posts_orderby && $orderby) {
      $orderby = $this->posts_orderby;
    }
    return $orderby;
  }
}
