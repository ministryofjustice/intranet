<?php if (!defined('ABSPATH')) die();

class Flush_rewrites extends MVC_controller {
  function main() {
    flush_rewrite_rules(false);
  }
}
