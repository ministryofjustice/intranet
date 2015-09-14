<?php

/**
 * This page routes the redirects from old intranet
 *
 * Template name: Redirect
 */


/**
Issues:
    [STATUS: description]
  - OPEN: many of the imported pages were written from scratch as new posts and they don't have the meta redirect_url
  - FIXED: the urls get modified because of some of our redirects (any occurrence of guidance-support gets replaced with guidance)
 */


class Route_redirects {
  function __construct() {
    global $wpdb;

    $this->settings = array(
      host => 'http://moj-wp-prod.s3.amazonaws.com/imported_content/'
    );

    $this->wpdb = $wpdb;
    $this->source_url = get_query_var('dw-source-url');

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
    }

    $this->redirect($redirect_url);
  }

  private function redirect($redirect_url) {
    if($redirect_url) {
      wp_redirect($redirect_url, 301);
    }
    else {
      wp_redirect(get_site_url() . '/404-redirect-not-found');
    }
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
}

new Route_redirects();
