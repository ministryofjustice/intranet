<?php if (!defined('ABSPATH')) die();

abstract class API {
  protected $cache_timeout = 60; //cache timeout in seconds
  protected $MVC;
  protected $params = array();
  private $method;

  function __construct() {
    global $MVC;
    $this->MVC = $MVC;
    $this->debug = (boolean) $_GET['debug'];
    $this->method = $_SERVER['REQUEST_METHOD'];
  }

  protected function response($data = array(), $status_code = 200, $cache_timeout = 60) {
    $date_format = 'D, d M Y H:i:s \G\M\T';

    if($this->debug) {
      Debug::full($data);
    }
    else {
      if($cache_timeout) {
        header('Cache-Control: public, max-age=' . $cache_timeout);
        header('Expires: '.gmdate($date_format, time() + ($cache_timeout?:60)));
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

  protected function error($message = 'Unspecified error') {
    $status_code = 401;

    $data = array(
      'status' => $status_code,
      'message' => $message
    );

    $this->response($data, $status_code, 0);

    exit();
  }

  protected function get_method() {
    return $this->method;
  }

  protected function get_param($name) {
    return $this->params[$name];
  }

  abstract protected function route();
}
