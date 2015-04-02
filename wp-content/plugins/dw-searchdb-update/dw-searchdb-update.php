<?php
/*
Plugin Name: dw-searchdb-update
Description: Combines fields to improve Relevanssi indexing
Plugin URI: https://github.com/ministryofjustice/mojintranet
Author: Ryan Jarrett
Author URI: http://sparkdevelopment.co.uk
Version: 1.0
*/

if (!defined('ABSPATH')) {
  exit; // disable direct access
}

if (!class_exists('DWDBUpdate')) {

  class DWDBUpdate {

    /**
     * Utility variable for hold variable variables
     * @var array
     */
    private $data;

    /**
     * Current plugin version
     * @var string
     */
    public $version = "1.0";

    /**
     * Keeps a tally of how many link records have been updated
     * @var integer
     */
    private $update_link_count = 0;

    /**
     * Keeps a tally of how many link records have been deleted
     * @var integer
     */
    private $delete_link_count = 0;

    /**
     * Keeps a tally of how many content records have been updated
     * @var integer
     */
    private $update_content_count = 0;

    /**
     * Keeps a tally of how many content records have been deleted
     * @var integer
     */
    private $delete_content_count = 0;

    /**
     * Constructor
     */
    public function __construct() {
      $this->data = array();
      $this->add_actions();
    }

    /**
     * Add actions and filters
     */
    private function add_actions() {
      add_action('admin_menu',array($this,'add_admin_page'));
      add_action('admin_enqueue_scripts',array($this, 'add_scripts'));
      add_action('wp_ajax_getgspages',array($this,'get_gs_pages'));
      add_action('wp_ajax_optimisedb',array($this,'update_db'));
      add_action('wp_ajax_rebuildindex',array($this,'rebuild_index'));
    }

    public function add_scripts() {
      wp_enqueue_script(
           'dwdbupdate'
          , plugins_url("/js/dwdbupdate.js",__FILE__)
          ,array('jquery')
      );
    }

    /**
     * Returns array of page IDs that user the Guidance & Support template
     * @return array Page IDs that use G&S template
     */
    public function get_gs_pages() {
      global $wpdb;

      /**
       * Query string to retrieve IDs of pages that use the Guidance and Support template
       * @var string
       */
      $querystring = "
        SELECT DISTINCT post_id
        FROM `wp_postmeta`
        WHERE `meta_key` LIKE '_wp_page_template'
        AND meta_value LIKE 'page-guidance-and-support.php'
      ";

      $page_ids = $wpdb->get_col($querystring);


      echo json_encode($page_ids);

      exit;
    }

    /**
     * Create page under admin Tools menu
     * @return [type] [description]
     */
    public function add_admin_page() {
      add_submenu_page( 'tools.php', 'Optimise database for G&S search', 'G&S DB Update', 'manage_options', 'dwdbupdate', array($this, 'show_admin_page') );
    }

    public function show_admin_page() {
      ?>
        <div class="wrap">
          <h2>Optimise database for G&S search</h2>
          <form method='post' action>
            <p>
              Pressing the button below will regenerate the fields that Relevanssi uses to index the content on the Guidance & Support pages.
              This should only be done when there is a problem with the search index and/or content has been imported from another source.
            </p>
            <p>
              <input type='submit' class='button hide-if-no-js' name='dwdbupdate-optimise' id='dwdbupdate-optimise' value='Optimise database for G&S search'>
            </p>
          </form>
          <div id="dwdbupdate-feedback"></div>
        </div>
      <?php
    }

    /**
     * Update database with combined fields
     * @return null
     */
    public function update_db() {
      $post_id = $_POST['postId'];
      $total_posts = $_POST['totalPosts'];
      $current_post = $_POST['currentPost'];
      $last_item = $_POST['last'];

      // Process links
      $this->update_links($post_id);
      $this->update_content($post_id);

      echo sprintf("
        <p>Post %d/%d processed</p>
        <p>Last ID processed: %d</p>
        ",
        $current_post,
        $total_posts,
        $post_id
        );

      exit;
    }

    function rebuild_index() {
      echo "<h3>Rebuilding index...</h3>";

      relevanssi_build_index();

      exit;
    }

    /**
     * Utility function for setting variable variables
     * @param string $varName Name of the variable
     * @param string $value   Value of the variable
     */
    public function __set($varName,$value){
      $this->data[$varName] = $value;
    }

    /**
     * Utility function for getting variable variables
     * @param  [type] $varName [description]
     * @return [type]          [description]
     */
    public function __get($varName){

      if (!array_key_exists($varName,$this->data)){
        //this attribute is not defined!
        throw new Exception('.....');
      }
      else return $this->data[$varName];

    }


    private function update_links($post_id) {
      $meta_key = "_quicklinks_search";
      $link_array = $this->get_link_array($post_id);

      $this->update_search_meta($post_id,$link_array,$meta_key,'link');
    }

    private function update_content($post_id) {
      $meta_key = "_tabs_search";
      $content_array = $this->get_content_array($post_id);

      $this->update_search_meta($post_id,$content_array,$meta_key,'content');
    }

    private function get_link_array($post_id) {
      // Populate link array
      $ns = 'quick_links'; // Quick namespace variable
      $link_array = array();

      $link_meta_exists = true;
      $i=1;

      while ($link_meta_exists) {
        $link_fields = array('link-text');
        if(metadata_exists( 'post', $post_id, "_" . $ns . "-link-text" . $i )) {
          foreach($link_fields as $link_field) {
              $link_field_transformed = str_replace('-','_',$link_field);
              $$link_field_transformed = get_post_meta($post_id, "_" . $ns . "-" . $link_field . $i,true);
          }
          if(strlen($link_text)) {
            $link_array[] = esc_attr($link_text);
          }
          $i++;
        } else {
          $link_meta_exists = false;
        }
      }

      return $link_array;
    }

    private function get_content_array($post_id) {
      // Populate content array
      $ns = 'content_tabs'; // Quick namespace variable
      $tab_count = get_post_meta($post_id,'_'.$ns.'-tab-count',true);

      $tab_array = array();
      for($i=1;$i<=$tab_count;$i++) {
        $tab_title = get_post_meta($post_id,'_'.$ns.'-tab-' . $i . '-title', true);
        $tab_title = esc_attr($tab_title);
        $tab_array[] = $tab_title;
        $section_count = get_post_meta($post_id,'_'.$ns.'-tab-' . $i . '-section-count',true);
        for($j=1;$j<=$section_count;$j++) {
          $section_title = get_post_meta($post_id,'_' . $ns . '-tab-' . $i . '-section-' . $j . '-title',true);
          $section_content = get_post_meta($post_id,'_' . $ns . '-tab-' . $i . '-section-' . $j . '-content-html',true);
          $tab_array[] = $section_title;
          $tab_array[] = wpautop($section_content);
        }
      }

      return $tab_array;
    }

    private function update_search_meta($post_id,$data_array,$meta_key,$type) {
      foreach($data_array as $data) {
        if ($type='content') {
          $data = WPCom_Markdown::get_instance()->transform( $data );
        }
        $combine .= $data . " ";
      }

      if(count($data_array)) {
        if (update_post_meta( $post_id, $meta_key, $combine )!=false) {
          $this->{"update_".$type."_count"}++;
        }
      } else {
        if (delete_post_meta( $post_id, $meta_key)!=false) {
          $this->{"delete_".$type."_count"}++;
        }
      }
    }

  }

  new DWDBUpdate;

}