<?php

$cache_timeout = 0;

include(get_template_directory() . '/inc/headers.php');

class Purge_cache extends MVC_Controller {
  function __construct($param_string, $post_id) {
    parent::__construct($param_string, $post_id);

    $this->debug = isset($_GET['debug']) && $_GET['debug'] ? true : false;
    $this->settings = [
      'node_count' => defined('DW_LOAD_BALANCER_NODES') ? (int) DW_LOAD_BALANCER_NODES : 1,
      'max_tries' => 50, //maximum number of attempts to hit all the nodes
    ];
  }

  /**
   * @param {String} $url base64-encoded url to purge
   */
  function main($url = '') {
    $url = base64_decode($url);
    $nodes = [];

    if ($this->debug) {
      Debug::full($url);
    }

    for ($count = 1; $count <= $this->settings['max_tries']; $count++) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_get_headers());
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PURGE');
      curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_NOBODY, !$this->debug);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 2000);

      $response = curl_exec($ch);
      curl_close($ch);

      if ($this->debug) {
        Debug::full($response);
      }

      preg_match('/X-Served-By:\s+([0-9a-fA-F]+)/', $response, $matches);

      if (count($matches) && $matches[1]) {
        $nodes[$matches[1]] = true;
      }

      if (count($nodes) >= $this->settings['node_count']) { //all nodes already hit?
        break;
      }
    }

    if (!count($nodes)) {
      $status = 'error';
      $message = 'Didn\'t hit a single node';
    }
    elseif (count($nodes) < $this->settings['node_count']) {
      $status = 'warning';
      $message = 'Only ' . count($nodes) . '/' . $this->settings['node_count'] . ' nodes were hit';
    }
    else {
      $status = 'success';
      $message = '(attempts: ' . $count . ')';
    }

    http_response_code(200);

    if ($this->debug) {
      Debug::full([$status, $message]);
    }
    else {
      header('Content-Type: application/json');
      echo json_encode([
        'status' => $status,
        'message' => $message
      ]);
    }
  }

  private function _get_headers() {
    return [
    ];
  }
}
