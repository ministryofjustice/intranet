<?php

/*
  Plugin Name: MenuAPI
  Description: An API that allows you to query the WordPress menus
  Author: Ryan Jarrett
  Version: 0.1
  Author URI: http://sparkdevelopment.co.uk
 */

if (!defined('ABSPATH')) {
    exit; // disable direct access
}

if (!class_exists('MenuAPI')) {

    class MenuAPI {

        /**
         * @var string
         */
        public $version = '0.1';

        /**
         * Define MenuAPI constants
         *
         * @since 1.0
         */
        private function define_constants() {

            define('MENUAPI_VERSION', $this->version);
            define('MENUAPI_BASE_URL', trailingslashit(plugins_url('menuapi')));
            define('MENUAPI_PATH', plugin_dir_path(__FILE__));
            define('MENUAPI_ROOT', 'service');
        }

        /**
         * All Mega Menu classes
         *
         * @since 1.0
         */
        private function plugin_classes() {

            return array(
                'menuapi_category_request' => MENUAPI_PATH . 'classes/category_request.php',
            );
        }

        public function __construct() {
            $this->define_constants();
            $this->includes();

            // Setup permalinks
            add_action('wp_loaded', array(&$this, 'flush_api_permalinks'));
            add_action('init', array(&$this, 'setup_api_rewrites'), 10);
            add_action('wp', array(&$this, 'process_api_request'), 5);
        }

        public function setup_api_rewrites() {
            add_rewrite_rule(MENUAPI_ROOT . '/(.+)/(.+)/*', 'index.php?api_action=$matches[1]&menuid=$matches[2]', 'top');
            add_rewrite_tag('%api_action%', '([^&]+)');
            add_rewrite_tag('%menuid%', '([^&]+)');
//            $wp->add_query_var('api_action');
//            $wp->add_query_var('menuid');
        }

        public function flush_api_permalinks() {
            global $wp_query;

            $rules = get_option('rewrite_rules');

//            if (!isset($rules['(' . MENUAPI_ROOT . ')/(.+)/(.+)$'])) {
            if (!isset($rules['(' . MENUAPI_ROOT . ')/(.+)$'])) {
                global $wp_rewrite;
                $wp_rewrite->flush_rules();
            }
        }

        public function process_api_request() {
            global $wp_query;

            // Get custom URL parameters
            $api_action = get_query_var('api_action');
            $menuid = get_query_var('menuid');

            if ($api_action !== '' && $menuid !== '') {
                $request_class = $api_action . "_request";
                new $request_class($menuid);
                exit;
            }
        }

        /**
         * Load required classes
         *
         * @since 1.0
         */
        private function includes() {

            foreach ($this->plugin_classes() as $id => $path) {
                if (is_readable($path) && !class_exists($id)) {
                    require_once $path;
                }
            }
        }

    }

    new MenuAPI;
}