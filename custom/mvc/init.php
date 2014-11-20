<?php if (!defined('ABSPATH')) die();

define('MVC_PATH', get_template_directory().'/mvc/');
define('MVC_VIEWS_DIR', 'views/');
define('MVC_VIEWS_PATH', MVC_PATH.MVC_VIEWS_DIR);

include_once('Loader.php');
include_once('Controller.php');
