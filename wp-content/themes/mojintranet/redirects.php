<?php

/**
 * This page routes the redirects from old intranet
 *
 * Template name: Redirect
 */
class Route_redirects {
  private static $excluded_keywords = array();
  private static $segment_limit = 2;

  function __construct() {
    global $wpdb;

    $this->settings = array(
      host => 'http://moj-wp-prod.s3.amazonaws.com/imported_content/'
    );

    $this->wpdb = $wpdb;
    $this->source_url = get_query_var('dw-source-url');
    $this->debug = (boolean) $_GET['dw_redirect_debug'];

    $this->begin();
  }

  private function begin() {
    $redirect_page = $this->get_redirect_page();
    $redirect_url = null;

    if($this->source_url) {
      if($redirect_page) { //try page redirects
        $redirect_url = get_permalink($redirect_page->ID);
      }
      else { //try doc redirects
        $results = $this->wpdb->get_results("SELECT * FROM dw_doc_urls WHERE absolute_url='".$this->source_url."' LIMIT 1");

        if(count($results)) {
          $redirect_url = $this->settings['host'] . $results[0]->path;
        }
      }

      if(!$redirect_url) {
        if(strpos($this->source_url, '/forms/') !== false) {
          $redirect_url = $this->get_forms_redirect_url();
        }
        elseif(strpos($this->source_url, '/Search.do?') !== false) {
          $redirect_url = $this->get_search_redirect_url();
        }
        else {
          $redirect_url = $this->get_fallback_url();
        }
      }
    }
    else {
      $redirect_url = site_url();
    }

    if($this->debug) {
      Debug::full($redirect_url);
      die();
    }

    //$this->redirect($redirect_url);
    wp_redirect($redirect_url, 301);
  }

  /** Finds a page with matching redirect url and returns its object
   * @return {Object|Null} The page object or null if no matching page was found
   */
  private function get_redirect_page() {
    $posts = get_posts(array(
      'meta_key' => 'redirect_url',
      'meta_value' => $this->source_url,
      'post_type' => 'page',
      'post_status' => 'publish',
      'posts_per_page' => -1
    ));

    return $posts[0] ?: null;
  }

  private function get_search_redirect_url() {
    preg_match('#\?(.*)#', $this->source_url, $matches);
    $query_string = $matches[1] ?: '';
    parse_str($query_string);

    $keywords = explode(' ', $query);

    return site_url('/search-results/all/' . implode('+', $keywords) . '/1/');
  }

  private function get_forms_redirect_url() {
    $forms_page_id = Taggr::get_id('forms-and-templates');

    return get_permalink($forms_page_id);
  }

  private function get_fallback_url() {
    $keywords = array();
    $flat_keywords = array();

    $url_segments = $this->get_limited_url_segments(Route_redirects::$segment_limit);

    foreach($url_segments as $segment) {
      $extracted_keywords = $this->extract_keywords($segment);

      foreach($extracted_keywords as $keyword) {
        $keywords[$keyword] = true; //using the keywords as array keys for deduping purposes
      }
    }

    //flatten the array
    foreach($keywords as $key=>$value) {
      $flat_keywords[] = $key;
    }

    if(!count($keywords)) {
      return site_url();
    }

    return site_url('/search-results/all/' . implode('+', $flat_keywords) . '/1/');
  }

  /** extracts the segments from the supplied url and returns them as an array
   * @param (Integer) $limit Limit the search for keywords to the last number of segments
   * @return (Array) Extracted segments
   */
  private function get_limited_url_segments($limit) {
    $limited_segments = array();
    $segment_counter = 0;

    //strip domain from the url
    $url = preg_replace('#http(s)?://[^/]+#', '', $this->source_url);
    $url = trim($url, '/');

    //strip extension, if any
    $url = preg_replace('/\.[A-Za-z0-9]{1,5}$/', '', $url); //removes extensions of up to 5 characters long

    $url_segments = strlen($url) ? explode('/', $url) : array();

    while(count($url_segments) && $segment_counter < $limit) {
      $segment_counter++;
      $limited_segments[] = array_pop($url_segments);
    }

    //get segments
    return $limited_segments;
  }

  /** extracts keywords from a string by splitting the string by non-alphanumeric characters
   * @param (String) $string Subject string
   * @return (Array) Extracted keywords
   */
  private function extract_keywords($string) {
    $string = preg_replace('/[^A-Za-z0-9]+/', ' ', $string);

    return strlen($string) ? explode(' ', $string) : array();
  }
}

new Route_redirects();
