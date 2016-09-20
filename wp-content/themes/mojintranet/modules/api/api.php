<?php if (!defined('ABSPATH')) die();

abstract class API {
  //!!! TODO: make params private and the getter/setter protected
  protected $MVC;
  protected $params = [];
  private $cache_timeout = 60; //cache timeout in seconds
  private $method;
  private $args = [
    'post' => [],
    'put' => []
  ];

  function __construct() {
    global $MVC;
    $this->MVC = $MVC;
    $this->debug = get_array_value($_GET, 'debug', false);
    $this->method = $_SERVER['REQUEST_METHOD'];

    if ($this->method == 'PUT' || $this->method == 'POST') {
      $parse_method = '_parse_' . strtolower($this->method);
      $this->$parse_method();
    }

    add_filter('posts_request', array($this, 'get_original_query'), 5);
  }

  /** Outputs the data as JSON
   * @param {Array} $data An array of data to be outputted
   * @param {Int} $status_code HTTP status code
   * @param {Int} $cache_timeout Cache timeout in seconds
   */
  protected function response($data = array(), $status_code = 200, $cache_timeout = 60) {
    $date_format = 'D, d M Y H:i:s \G\M\T';

    if ($this->debug) {
      Debug::full($this->original_query);
      Debug::full($data);
    }
    else {
      if ($cache_timeout) {
        header('Cache-Control: public, max-age=' . $cache_timeout);
        header('Expires: '.gmdate($date_format, time() + $cache_timeout));
        header_remove("Pragma");
      } else {
        header('Cache-Control: private, max-age=0, no-cache');
        header("Pragma: no-cache");
        header('Expires: '.gmdate($date_format, time() - 60));
      }

      header('Content-Type: application/json');
      http_response_code($status_code);
      echo json_encode($data);
    }
  }

  /** Outputs an error message using response() method
   * @param {String} $message Error message
   */
  protected function error($message = 'Unspecified error', $status_code = 401) {
    $data = array(
      'status' => $status_code,
      'message' => $message
    );

    $this->response($data, $status_code, 0);

    exit();
  }

  /** Get the HTTP method used
   * @return {String} HTTP method, e.g. GET, POST, PUT, DELETE
   */
  protected function get_method() {
    return $this->method;
  }

  /** Get a url param (segment) by key, as defined in a given API
   * Example of search API:
   *   We're accessing the API using: http://mydomain.com/service/search/my+search+terms/1/10
   *   The search API names the segments as such:
   *      Segment[0] -> keywords (my+search+terms)
   *      Segment[1] -> page (1)
   *      Segment[2] -> results_per_page (10)
   *   get_param('page') will return 1
   *
   * @param {String} $key Name of the key
   * @return {String} The value of the param
   */
  protected function get_param($key) {
    return get_array_value($this->params, $key, '');
  }

  /** gets taxonomies based on url segments
   * @param {Array} $options Options for WP Query
   * @return {Array} $options with added taxonomies
   */
  protected function add_taxonomies($options = array()) {
    $agency = $this->get_param('agency') ?: 'hq';
    $additional_filters = urldecode($this->get_param('additional_filters')) ?: '';
    $taxonomies = array('relation' => 'AND');
    $filters = array('agency=' . $agency);

    if (strlen($additional_filters)) {
      $filters = array_merge($filters, explode('&', $additional_filters));
    }

    foreach ($filters as $filter) {
      $pair = explode('=', $filter);
      $taxonomy = $pair[0];
      $terms = explode('|', $pair[1]);

      if (taxonomy_exists($taxonomy)) {
        $taxonomies[] = array(
          'taxonomy' => $pair[0],
          'field' => 'slug',
          'terms' => $terms
        );
      }
    }

    $options['tax_query'] = $taxonomies;

    return $options;
  }

  /** Get POST value by key
   * @param {String} $key Name of the post key
   * @return {String} The value of the post key
   */
  protected function post($key = null) {
    return $key ? $this->args['post'][$key] : $this->args['post'];
  }

  /** Get PUT value by key
   * @param {String} $key Name of the put key
   * @return {String} The value of the put key
   */
  protected function put($key = null) {
    return $key ? $this->args['put'][$key] : $this->args['put'];
  }

  /** Retrieve and store the POST values
   */
  private function _parse_post() {
    $this->args['post'] = $_POST;
  }

  /** Retrieve and store the PUT values
   */
  private function _parse_put() {
    parse_str(file_get_contents('php://input'), $this->args['put']);
  }

  /** Route the traffic based on HTTP method (and params) used
   */
  abstract protected function route();

  /** Get the original WP SQL query before it gets processed by relevanssi
   */
  public function get_original_query($request) {
    $this->original_query = $request;
    return $request;
  }
}
