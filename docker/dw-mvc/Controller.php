<?php if (!defined('ABSPATH')) die();

abstract class MVC_controller extends MVC_loader {

  /**
   *  @param {String} $param_string The url segments - see below (only used for true controllers)
   *  @param {Integer} $post_id Post ID (only used for post-based controllers)
   *
   *  Examples of url segments:
   *
   *  /controller_name/segment1/segment2/
   *    segments: segment1/segment2/
   *
   *  /service/hq//news/
   *    segments: hq//news/
   */
  function __construct($param_string = '', $post_id) {
    global $MVC;

    parent::__construct();

    _wp_admin_bar_init(); //needed for wp_head() and possibly for wp_footer()

    if (!$MVC) {
      $MVC = $this;
      $this->_load_default_models();
    }

    $this->post_id = $post_id;
    $this->_get_segments($param_string);
    $this->wp_head = $this->_get_wp_header();
    $this->wp_footer = $this->_get_wp_footer();
  }

  /** Adds multiple global view vars
   * @param {Array} $data Array of data to be added as global view data
   *
   */
  public function add_global_view_data($data) {
    foreach ($data as $key => $value) {
      $this->add_global_view_var($key, $value);
    }
  }

  /** Adds a global view variable
   * This variable will be available in all views called from this controller
   * and in all subviews called from these views.
   * @param {String} $name Name
   * @param {Mixed} $value Value
   */
  public function add_global_view_var($name, $value) {
    $this->global_view_data[$name] = $value;
  }

  public function run() {
    if (method_exists($this, $this->method)) {
      call_user_func_array([$this, $this->method], $this->segments);
    }
    else {
      header("Location: " . site_url());
    }
  }

  public function output_json($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
  }

  private function _get_segments($param_string) {
    $segments = explode('/', $param_string);
    $this->method = array_shift($segments) ?: 'main';
    $this->segments = $segments;
  }

  private function _get_wp_header() {
    ob_start();
    wp_head();
    return ob_get_clean();
  }

  private function _get_wp_footer() {
    ob_start();
    wp_footer();
    return ob_get_clean();
  }
}
