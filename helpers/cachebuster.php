<?php if (!defined('ABSPATH')) die();

class Cachebuster {
  private $theme_path;
  private $checksums_file;
  private $assets_dir;

  function __construct() {
    $this->theme_path = 'wp-content/themes/mojintranet/';
    $this->checksums_file = $this->theme_path . 'checksums.json';
    $this->assets_dir = $this->theme_path . 'assets/';

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
