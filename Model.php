<?php if (!defined('ABSPATH')) die();

abstract class MVC_model {
  function __construct() {
		foreach (MVC_loader::$models as $model => $instance) {
			$this->$model =& $instance;
		}
  }
}
