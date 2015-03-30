<?php if (!defined('ABSPATH')) die();

class Cachebuster {
  private $checksums_file = 'checksums.json';
  private $assets_dir = 'wp-content/themes/govintranetpress/';

  function __construct() {
    if(file_exists($this->checksums_file)) {
      $this->checksums = json_decode(file_get_contents($this->checksums_file), true);
    }
    else {
      $this->checksums = null;
    }
  }

  function add_checksum_param($file_path) {
    if($this->checksums[$this->assets_dir.$file_path]) {
      return 'checksum='.$this->checksums[$this->assets_dir.$file_path];
    }
    else {
      return '';
    }
  }
}

$cachebuster = new Cachebuster();

function add_checksum_param($file) {
  global $cachebuster;
  return $cachebuster->add_checksum_param($file);
}
