<?php if (!defined('ABSPATH')) die();

class Search_model extends MVC_model {
  private $options = array(); //all search options
  private $meta_query = array(); //stores SQL query parts responsible for checking the existence of meta data
  private $date_query = array();
  private $posts_orderby = '';

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
    add_filter('posts_orderby', array($this, 'wp_filter_posts_orderby'));

    $this->options = $this->initialise_options($options);

    //process the query
    $this->build_meta_clause();
    $results = $this->get_raw_results();
    $data = $this->get_formatted_data($results);

    $data['total_results'] = $results->found_posts;

    remove_filter('posts_orderby', array($this, 'wp_filter_posts_orderby'));

    return $data;
  }

  private function initialise_options($options) {
    $default = array(
      'search_orderby' => array('relevance' => 'ASC'),
      'meta_fields' => array(),
      'page' => 1,
      'per_page' => 10,
      'post_type' => 'all',
      'keywords' => ''
    );

    foreach($options as $key=>$value) {
      $default[$key] = $value;
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
		$args = array(
			// Paging
			'nopaging' => false,
			'paged' => $this->options['page'],
			'posts_per_page' => $this->options['per_page'],
			// Sorting
			'orderby' => $this->options['search_orderby'],
			// Filters
			'post_type' => $this->options['post_type'],
			's' => $this->rawurldecode($this->options['keywords']),
			'meta_query' => $this->meta_query,
			'date_query' => $this->date_query
		);

    return new WP_Query($args);
  }

  private function get_formatted_data($results) {
    if (function_exists(relevanssi_do_query) && $this->options['keywords'] != null) {
      relevanssi_do_query($results);
    }

    $data = array(
      'total_results' => 0,
      'results' => array()
    );

    foreach($results->posts as $post) {
      $data['results'][] = $this->format_row($post);
    }

    return $data;
  }

  private function format_row($post) {
    $id = $post->ID;

    the_post($id);

    $titles = array();
    $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'thumbnail');
    $page_ancestors = get_post_ancestors($id);
    $excerpt = $post->post_type == 'document' ? '' : $post->post_excerpt;

    if (count($page_ancestors)) {
      $titles = array_slice($page_ancestors, -2);
      foreach($titles as $index => $item) {
        $titles[$index] = get_post_meta($item, 'nav_label', true) ?: get_the_title($item);
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
      'timestamp' => (string) get_the_time('Y-m-d H:i:s'),
      'file_url' => (string) '',
      'file_name' => (string) '',
      'file_size' => (int) 0,
      'file_pages' => (int) 0,
      'content_type' => $titles
    );
  }

  private function build_meta_clause() {
		// Build meta_query (and extend orderby)
		if(count($this->options['meta_fields'])) {
			foreach ($this->options['meta_fields'] as $meta_field) {
        $this->meta_query[$meta_field] = array(
          'key' => $meta_field,
          'compare' => 'EXISTS'
        );
			}

      //temporary fix for a bug in WP core
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
