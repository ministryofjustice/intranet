<?php if (!defined('ABSPATH')) die();

/*
 * Template name: Service
 */

class Service extends MVC_controller {
  function __construct($param_string) {
    parent::__construct();
    $this->params = explode('/', $param_string);
    $this->api = array_shift($this->params);
  }

  function main() {
    $this->load_api('api');
    $this->load_api($this->api, true);
  }

  private function load_api($api, $instantiate = false) {
    $api_path = get_template_directory() . '/modules/api/' . $api .'.php';
    $api_classname = ucfirst($api) . '_API';

    if(file_exists($api_path)) {
      include_once($api_path);

      if($instantiate) {
        $this->api_instance = new $api_classname($this->params);
      }
    }
    else {
      $response = array (
        "status"    => 401,
        "message"   => "Endpoint not valid",
        "more_info" => "https://github.com/ministryofjustice/dw-api/blob/master/README.md"
      );

      header('Content-Type: application/json');
      http_response_code($status_code);
      echo json_encode($response);
    }
  }
}
