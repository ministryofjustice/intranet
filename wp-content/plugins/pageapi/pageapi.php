<?php

/*
  Plugin Name: PageAPI
  Description: An API that allows you to query the WordPress page structure
  Author: Ryan Jarrett
  Version: 0.2
  Author URI: http://sparkdevelopment.co.uk

  Changelog
  ---------
  0.1 - initial release - children request class added
  0.2 - search request class added; api_request class added
 */

if (!defined('ABSPATH')) {
    exit; // disable direct access
}

if (!class_exists('PageAPI')) {

    class PageAPI {

        /**
         * @var string
         */
        public $version = '0.2';

        /**
         * Define PageAPI constants
         *
         * @since 1.0
         */
        private function define_constants() {

            define('PAGEAPI_VERSION', $this->version);
            define('PAGEAPI_BASE_URL', trailingslashit(plugins_url('pageapi')));
            define('PAGEAPI_PATH', plugin_dir_path(__FILE__));
            define('PAGEAPI_ROOT', 'service');
        }

        /**
         * All PageAPI classes
         *
         * @since 1.0
         */
        private function plugin_classes() {
            return array(
                'api_request' => PAGEAPI_PATH . 'classes/api_request.php',
                'children_request' => PAGEAPI_PATH . 'classes/children_request.php',
                'search_request' => PAGEAPI_PATH . 'classes/search_request.php',
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

        /**
        * Set up rewrite rules in WordPress
        *
        * @since 1.0
        */
        public function setup_api_rewrites() {
          $total_params=0;
          foreach ($this->plugin_classes() as $id => $path) {
            if (class_exists($id)) {
              // $temp_class = new $id(true);
              if($total_params<count($id::$params)) {
                $total_params = count($id::$params);
              }
            }
          }
          $rewrite_string = 'index.php?api_action=$matches[1]';
          $rewrite_pattern = '/([^/]+)';
          for($i=1;$i<=$total_params;$i++) {
            $rewrite_string .= '&param' . ($i) . '=$matches[' . ($i+1) . ']';
            $rewrite_pattern .= '/?([^/]*)?';
            add_rewrite_tag('%param' . ($i) . '%', '([^&]+)');
          }

          add_rewrite_rule(PAGEAPI_ROOT . $rewrite_pattern . '/?', $rewrite_string, 'top');
          add_rewrite_tag('%api_action%', '([^&]+)');

          // global $wp_rewrite;var_dump($wp_rewrite);
        }

        /**
        * Reset permalinks in WordPress
        *
        * @since 1.0
        */
        public function flush_api_permalinks() {
            global $wp_query;

            $rules = get_option('rewrite_rules');

            if (!isset($rules['(' . PAGEAPI_ROOT . ')/(.+)$'])) {
                global $wp_rewrite;
                $wp_rewrite->flush_rules();
            }
        }

        /**
        * Parses endpoint and processes API request
        *
        * @since 1.0
        */
        public function process_api_request() {
            global $wp_query;

            // Get custom URL parameters
            $api_action = get_query_var('api_action');

            if ($api_action !== '') {
                $request_class = $api_action . "_request";
                $results = new $request_class();
                $this->output_json($results);
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

        /**
         * Outputs JSON from results array
         *
         * @since 1.0
         */
        function output_json($json_array) {
            echo json_encode($json_array->results_array);
        }

    }

    new PageAPI;
}