<?php if (!defined('ABSPATH')) die();

abstract class MVC_model {
  function __construct() {
		global $MVC;
		$this->model = $MVC->get_model_object();
  }
}
