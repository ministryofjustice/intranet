<?php

class Purge_cache extends MVC_Controller {
  function __construct($param_string, $post_id) {
    parent::__construct($param_string, $post_id);
    $this->settings = [
      'node_count' => 5,
      'max_tries' => 50, //maximum number of attempts to hit all the nodes
    ];
  }

  /**
   * @param {String} $url base64-encoded url to purge
   */
  function main($url = '') {
    $url = base64_decode($url);
    $nodes = [];

    for ($count = 1; $count <= $this->settings['max_tries']; $count++) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_get_headers());
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PURGE');
      curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_NOBODY, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 2000);

      $response = curl_exec($ch);
      curl_close($ch);

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

    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode([
      'status' => $status,
      'message' => $message
    ]);
  }

  private function _get_headers() {
    return [
    ];
  }
}
