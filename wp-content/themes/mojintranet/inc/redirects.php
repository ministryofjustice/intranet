<?php
function dw_redirects() {
  $path = $_SERVER['REQUEST_URI'];

  //Search form -> search results page
  if(isset($_POST['s']) || $_POST['search-filter'] ) {
    $keywords = $_POST['s'] ?: '-';
    $keywords = rawurlencode(stripslashes($keywords));
    $keywords = str_replace('%2F', '%252F', $keywords);
    $keywords = str_replace('%5C', '%255C', $keywords);
    $filter = $_POST['search-filter'] ?: 'all';

    header('Location: ' . site_url() . '/search-results/' . $filter . '/' . $keywords . '/1/');
    exit;
  } elseif (preg_match('/\/search\/?$/',$path)) {
    header('Location: ' . site_url());
    exit;
  }

  if(strpos($path, 'guidance-and-support') || strpos($path, 'guidance-support')) {
    $new_path = preg_replace('/^([^?]*)(guidance-and-support|guidance-support)/', '${1}guidance', $path);
    if($new_path != $path) {
      header('Location: ' . site_url() . $new_path);
      exit;
    }
  }
}

function dw_rewrite_rules() {
  /**
  Remember!
  If you want "something/" to be accessible (i.e. with trailing slash), make sure to use "/?" in the rule.
  The reason why it is required is unknown, must be colliding with some other rule being applied behind the scenes.
   */

  //register url parameters
  add_rewrite_tag('%search-filter%', '([^&]+)');
  add_rewrite_tag('%search-string%', '([^&]+)');

  //News page
  $regex = '^newspage/page/([0-9]+)/(.*)';
  $redirect = 'index.php?page_id=' . get_page_by_path('newspage')->ID;
  add_rewrite_rule($regex, $redirect, 'top');

  //Blog page
  $regex = '^blog/page/([0-9]+)/(.*)';
  $redirect = 'index.php?page_id=' . get_page_by_path('blog')->ID;
  add_rewrite_rule($regex, $redirect, 'top');

  //Events page
  $regex = '^events/([0-9]+)(/.*)?';
  $redirect = 'index.php?page_id=' . get_page_by_path('events')->ID;
  add_rewrite_rule($regex, $redirect, 'top');

  //Search results page
  $regex = '^search-results/([^/]*)/([^/]*)/?';
  $redirect = 'index.php?page_id=' . get_page_by_path('search-results')->ID . '&search-filter=$matches[1]&search-string=$matches[2]';
  add_rewrite_rule($regex, $redirect, 'top');

  //Webchat archive page
  $regex = '^webchats/archive/?';
  $redirect = 'index.php?page_id=' . get_page_by_path('webchats/archive')->ID;
  add_rewrite_rule($regex, $redirect, 'top');

  // ping.json
  $regex = '^ping.json';
  $redirect = 'wp-content/themes/mojintranet/ping.php';
  add_rewrite_rule($regex, $redirect, 'top');

  //Custom controllers
  $regex = '^(service|password|create-an-account|sign-in)(/(.*)|$)';
  $redirect = 'index.php?controller=$matches[1]&param_string=$matches[3]';
  add_rewrite_rule($regex, $redirect, 'top');

}
add_action('init', 'dw_redirects');
add_action('init', 'dw_rewrite_rules');

function redirect_404($template) {
  $error_template = locate_template( 'page_error.php' );
  if($error_template!='') {
    return $error_template;
  }
}
add_action('404_template','redirect_404',99);

class Route_redirects {
  private static $excluded_keywords = array();
  private static $segment_limit = 2;

  function __construct() {
    global $wpdb;

    $this->settings = array(
        host => 'http://moj-wp-prod.s3.amazonaws.com/imported_content/'
    );

    $this->wpdb = $wpdb;
    $this->source_url = $_GET['dw-source-url'];
    $this->debug = (boolean) $_GET['dw_redirect_debug'];

    $this->begin();
  }

  private function begin() {
    $redirect_page = $this->get_redirect_page();
    $redirect_url = null;

    if ($this->source_url) {
      if ($redirect_page) { //try page redirects
        $redirect_url = get_permalink($redirect_page->ID);
      }
      else { //try doc redirects
        $results = $this->wpdb->get_results("SELECT * FROM dw_doc_urls WHERE absolute_url='".$this->source_url."' LIMIT 1");

        if (count($results)) {
          $redirect_url = $this->settings['host'] . $results[0]->path;
        }
      }

      if (!$redirect_url) {
        if (strpos($this->source_url, '/forms/') !== false) {
          $redirect_url = $this->get_forms_redirect_url();
        }
        elseif (strpos($this->source_url, '/Search.do?') !== false) {
          $redirect_url = $this->get_search_redirect_url('query');
        }
        elseif (strpos($this->source_url, '/SearchPEP.do?') !== false) {
          $redirect_url = $this->get_search_redirect_url('q');
        }
        else {
          $redirect_url = $this->get_fallback_url();
        }
      }
    }
    else {
      $redirect_url = site_url();
    }

    if ($this->debug) {
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

  private function get_search_redirect_url($param_name) {
    preg_match('#\?(.*)#', $this->source_url, $matches);
    $query_string = $matches[1] ?: '';
    parse_str($query_string, $params);

    $keywords = explode(' ', $params[$param_name]);

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

    foreach ($url_segments as $segment) {
      $extracted_keywords = $this->extract_keywords($segment);

      foreach ($extracted_keywords as $keyword) {
        $keywords[$keyword] = true; //using the keywords as array keys for deduping purposes
      }
    }

    //flatten the array
    foreach ($keywords as $key=>$value) {
      $flat_keywords[] = $key;
    }

    if (!count($keywords)) {
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
    $url = str_replace('index.htm', '', $url);
    $url = str_replace('my-services', '', $url);
    $url = preg_replace('#//+#', '/', $url);
    $url = trim($url, '/');

    //strip extension, if any
    $url = preg_replace('/\.[A-Za-z0-9]{1,5}$/', '', $url); //removes extensions of up to 5 characters long

    $url_segments = strlen($url) ? explode('/', $url) : array();

    while (count($url_segments) && $segment_counter < $limit) {
      $segment_counter++;
      $limited_segments[] = array_pop($url_segments);
    }

    if ($this->debug) {
      Debug::full($limited_segments);
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


function dw_redirect_page()
{
  global $wp;
  $current_url = home_url(add_query_arg(array(),$wp->request));

  if ($current_url == site_url('redirect')) {
    new Route_redirects();
  }
}
add_action( 'wp', 'dw_redirect_page', 1);
