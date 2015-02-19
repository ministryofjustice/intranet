<?php

/*
  Plugin Name: dw-crawl-import
  Description: Interface to allow importing of Guidance & Support page data in MOJ Intranet
  Author: Ryan Jarrett
  Version: 0.1
  Author URI: http://sparkdevelopment.co.uk
*/

  if (!defined('ABSPATH')) {
    exit; // disable direct access
  }

  if (!class_exists('CrawlImport')) {

    class CrawlImport {

      /**
       * @var string
       */
      public $version = '0.1';

        /**
         * Define crawl-import constants
         * @since 0.1
         */
        private function define_constants() {

          define('CI_ENDPOINT', 'crawl-import');
        }

      /**
       * Constructor for CrawlImport
       * @since 0.1
       */
      public function __construct() {
        $this->define_constants();

        // Add endpoint
        add_action('wp_loaded', array(&$this, 'flush_api_permalinks'));
        add_action('init', array(&$this, 'setup_endpoint'), 10);
        add_action('wp', array(&$this, 'update_page'), 10);

      }

      /**
       * Creates rewrite rules for CrawlImport rewrite URL
       * @since 0.1
       */
      public function setup_endpoint() {
        /**
         * The rewrite URL for the endpoint
         * @var string
         */
        $rewrite_string = 'index.php?crawl_import=1&crawl_switch=$matches[1]';

        add_rewrite_rule(CI_ENDPOINT.'/([^/]+)/?',$rewrite_string,'top');
        add_rewrite_tag('%crawl_import%', '([^&]+)');
        add_rewrite_tag('%crawl_switch%', '([^&]+)');
        // global $wp_rewrite;var_dump($wp_rewrite);
      }

      /**
      * Reset permalinks in WordPress
      * @since 1.0
      */
      public function flush_api_permalinks() {
        global $wp_query;

        $rules = get_option('rewrite_rules');

        if (!isset($rules['(' . CI_ENDPOINT . ')/(.+)$'])) {
          global $wp_rewrite;
          $wp_rewrite->flush_rules();
        }
      }

      /**
       * Update the page (post) with the provided data
       * @since 0.1
       * @return  boolean|Whether the page (post) was updated
       */
      public function update_page() {
        global $wp_query;

        /**
         * Hold results of the API call
         * @var array
         */
        $results_array = array();

        /**
         * The source json string
         * @var string
         */
        $source_json = $_POST['import_json'];

        /**
         * The decoded json object
         * @var object
         */
        $source = json_decode(stripslashes($source_json));

        /**
         * The flag used to activate the import
         * @var string
         */
        $crawl_set = get_query_var('crawl_import');

        /**
         * The switch used to set importer options
         * @var string
         */
        $crawl_switch = get_query_var('crawl_switch');
        if ($crawl_set===1) {
          if(strlen($crawl_switch)<1) {
            // Process import object
            $output_ns = "_content_tabs-";
            $post_id = $source->id;

            // Process data string
            $import_data = json_decode($source->data);
            $postarr = array(
              'ID' => $post_id,
              'post_title' => $import_data->title
            );
            wp_update_post($postarr);

            // Collect metadata
            $tab_count = sizeof($import_data->tabs);
            $meta_array['tab-count'] = $tab_count;
            // Process tabs
            for($i=1;$i<=$tab_count;$i++) {
              $current_tab = $import_data->tabs[$i-1];
              $section_count = sizeof($current_tab->sections);
              $meta_array['tab-' . $i . '-title'] = $current_tab->name;
              $meta_array['tab-' . $i . '-section-count'] = $section_count;
              for($j=1;$j<=$section_count;$j++) {
                $current_section = $current_tab->sections[$j-1];
                $meta_array['tab-' . $i . '-section-' . $j . '-title'] = $current_section->title;
                $meta_array['tab-' . $i . '-section-' . $j . '-content'] = $current_section->content;
                $meta_array['tab-' . $i .'-section-' . $j . '-content-html'] = $current_section->content;
              }
            }

            // Process metadata
            foreach($meta_array as $key=>$value) {
              update_post_meta($post_id,$output_ns.$key,$value);
            }


          } else {
            switch ($crawl_switch) {
              case 'form':
                // Build form for testing
                $api_path = site_url(CI_ENDPOINT);
                ?>
                <form method="post" action="<?=$api_path?>">
                  <textarea rows=20 cols=80 placeholder="Paste JSON here..." id="import_json" name="import_json"></textarea>
                  <input type='submit'>
                </form>
                <?php
                exit;
                break;
              default:
                $results = array(
                  "status"    => 401,
                  "message"   => "Switch not permitted",
                  "more_info" => ""
                );
                http_response_code(401);
                $this->output_json($results);
            }
          }
          $results = array(
            'import_status'=>true,
            'import_flag'=>$crawl_switch
            );
        }
      }

      function output_json($results_array) {
        header('Content-Type: application/json');
        echo json_encode($results_array);
        exit;
      }

    }

    new CrawlImport;

  }