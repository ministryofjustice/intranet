<?php

/* Converts Guidance Tab Sections from Markdown to HTML and then saves in additional Meta Field */
add_action('acf/save_post', 'dw_save_guidance_tabs', 20);
function dw_save_guidance_tabs($post_id) {
  if (have_rows('guidance_tabs', $post_id)) {
    $tab_i = 0;

    while (have_rows('guidance_tabs', $post_id)) {
      the_row();

      $tab_i++;

      if (have_rows('sections')) {
        $section_i = 0;

        while (have_rows('sections')) {
          the_row();

          $section_i++;
          $section_content = get_sub_field('section_content', false);
          update_sub_field( array('guidance_tabs', $tab_i, 'sections', $section_i, 'section_html_content'), WPCom_Markdown::get_instance()->transform($section_content) );
        }
      }
    }
  }
}

/* Dynamic filtering of Parent pages */
add_action('wp_ajax_check_parent', 'pageparent_ajax_check_parent');
function pageparent_ajax_check_parent() {
  global $wpdb;

  $context = Agency_Context::get_agency_context();

  $filter_data = $_POST['data'];
  $filter_text = sanitize_text_field($filter_data["filtertext"]);
  $current_page = intval($filter_data["pageID"]);
  $post_type = get_post_type($current_page);

  $parent_query = "SELECT ID,post_title,post_parent,post_type,post_status FROM $wpdb->posts
                   LEFT JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id )
                   LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id )
                   LEFT JOIN $wpdb->terms ON ( $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id )
                   WHERE post_title LIKE '%%%s%%'
                   AND $wpdb->posts.ID != %s
                   AND post_type = '%s'
                   AND post_status IN ('publish','draft')
                   AND $wpdb->term_taxonomy.taxonomy = 'agency'
                   AND $wpdb->terms.slug IN ( 'hq', '%s' ) ";

  if($post_type == 'regional_page' && Region_Context::current_user_can_have_context()) {
    $term_id = Region_Context::get_region_context('term_id');
    $parent_query .= "AND $wpdb->posts.ID IN
                        (SELECT object_id FROM  $wpdb->term_relationships
                         LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id )
                         WHERE $wpdb->term_taxonomy.term_id = $term_id
                        )
                      ";
  }

  $parent_query .= "GROUP BY $wpdb->posts.ID
                    ORDER BY post_title LIMIT 0,30";

  $parentname = $wpdb->get_results( $wpdb->prepare($parent_query, array($filter_text, $current_page, $post_type, $context)));
  if($parentname) {
    foreach ($parentname as $parent) {
      $parent_title = get_the_title($parent->post_parent);
      if ($parent_title!='') {
        $parent_title = $parent_title."&nbsp;>><br>";
      }

      $statecheck = get_post($parent->post_parent);
      $parent_state = '';
      if (isset($statecheck)) {
        $parent_state = get_the_title($statecheck->post_parent);
        if ($parent_state != '') {
          $parent_state = $parent_state . "&nbsp;> ";
        }
      }

      $page_status = '';
      if($parent->post_status == 'draft'){
        $page_status = ' (Draft)';
      }

      echo "<li class='pageparentoption'>
        <a class=\"parentlink\" style=\"cursor:pointer;\" parentname='". esc_html($parent->post_title) . "' parentid='" . $parent->ID . "'>
          <small>".
            $parent_state." ".$parent_title."
          </small> ".
          $parent->post_title . $page_status . "
        </a>
      </li>\n";
    }
    echo '<script language="javascript" type="text/javascript">
        jQuery(".parentlink").bind(\'click\', function() {
          var parentid = jQuery(this).attr(\'parentid\');
          var parentname = jQuery(this).attr(\'parentname\');
          jQuery(this).css(\'font-weight\',900);
          jQuery(".parentlink").parent(\'li\').hide("slow");
          jQuery(this).parent(\'.pageparentoption\').show();
          jQuery("#pageparent-filterbox").val(parentname);
          jQuery("#parent_id").val(parentid);
        });
    </script>';
    wp_die();
  } else {
    echo "<p>No matching pages found</p>";
    wp_die();
  }
}

add_action('admin_menu', 'pageparent_add_theme_box');
function pageparent_add_theme_box() {
  if ( ! is_admin() )
    return;
  add_meta_box('pageparent-metabox', __('Parent Page'), 'pageparent_box', ['page', 'regional_page'], 'side', 'core');
}

function pageparent_box($post) {
  echo '<input type="hidden" name="taxonomy_noncename" id="taxonomy_noncename" value="' . wp_create_nonce( 'taxonomy_theme' ) . '"/>';

  //get current parent
  global $post;
  $load_image_url =  get_template_directory_uri() . '/admin/images/pageparent.gif';
  $parent_page = wp_get_post_parent_id($post->ID);
  $disabled = '';

  if (get_post_type($post->ID) == 'page' || current_user_can('administrator')) {
    //populate template list
    if (get_post_type($post->ID) == 'page') {
      $current_template = get_post_meta($post->ID, '_wp_page_template', true);

      if (in_array($current_template, Agency_Editor::$restricted_templates) && !current_user_can('administrator')) {
        $disabled = 'disabled="disabled"';
      }

      $templates = get_page_templates();
    } else if (get_post_type($post->ID) == 'regional_page') {
      $current_template = get_post_meta($post->ID, 'dw_regional_template', true);

      $templates = [
          'Generic' => 'page_generic_nav.php',
          'Landing' => 'page_regional_landing.php',
          'Events Listing' => 'page_regional_events.php',
          'Updates Listing' => 'page_regional_news.php',
      ];
    }

    $themeselect = '<select id="page_template" name="page_template" ' . $disabled . '>';
    foreach ($templates as $template_name => $template_filename) {
      if (!in_array($template_filename, Agency_Editor::$restricted_templates) || $current_template == $template_filename || current_user_can('administrator')) {
        $select = $current_template == $template_filename ? 'selected="selected"' : "";
        $themeselect .= '<option value="' . $template_filename . '" ' . $select . '>' . $template_name . '</option>';
      }
    }
    $themeselect .= '</select>';
    ?>
    <p><strong>Current Template:</strong></p>
    <?php
    echo $themeselect;
  }
  ?>
  <p><strong>Current Parent:</strong></p>

    <div>
      <?php

      if ($parent_page) {
        echo get_the_title($parent_page);
      }
      else {
        echo 'None';
      }
      ?>
    </div>

  <p><strong>New Parent Page:</strong></p>
  <input type="text" name="pageparent-filterbox" id="pageparent-filterbox" autocomplete="off" placeholder="Start typing...">
  <input type="hidden" name="parent_id" id="parent_id" readonly="readonly" value="<?php echo $post->post_parent; ?>">
  <div id="pageparent-result"></div>

  <script language="javascript" type="text/javascript">
    jQuery(document).ready(function() {
      var timer;

      var checkparent = function () {
        jQuery.post(
          ajaxurl,
          {
             'action': 'check_parent',
             'data'  : { filtertext: jQuery("#pageparent-filterbox").val() , pageID: <?php echo $post->ID; ?> }
          }
        ).done( function(response) {
          if(response.length) {
            jQuery( "#pageparent-result" ).empty().append(response);
          } else {
            jQuery( "#pageparent-result" ).empty().append("No matching pages found");
          }
        })
      };

      var updateResults = function() {
        jQuery( "#pageparent-result" ).empty().append( '<img src="<?php echo $load_image_url; ?>" alt="" class="loading">');
        timer && clearTimeout(timer);
        timer = setTimeout(checkparent, 250);
      };

      jQuery("#pageparent-filterbox").on('keypress',function() {
        updateResults();
      }).on('keydown', function(e) {
         if (e.keyCode==8) updateResults();
      });

      jQuery(".parentlink").click(function() {
        var parentid = jQuery(this).attr('parentid');
        var parentname = jQuery(this).attr('parentname');
        jQuery("#pageparent-filterbox").val(parentname);
        jQuery("#parent_id").val(parentid);
      });
    });
  </script>

<?php
}

add_action('admin_menu', 'pageparent_remove_theme_box');
function pageparent_remove_theme_box() {
  // Remove default parent metabox
  if ( ! is_admin() )
    return;
  remove_meta_box('pageparentdiv', 'page', 'side');
}

function remove_post_custom_fields() {
  if (!current_user_can('administrator')) {
    foreach (get_post_types( array('public' => true), 'names' ) as $post_type) {
      remove_meta_box('postcustom', $post_type, 'normal');
    }
  }
}
add_action('admin_menu' , 'remove_post_custom_fields');

/**
 * Save regional template meta value
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */
function dw_save_regional_template($post_id, $post, $update) {
  if (get_post_type($post_id) != 'regional_page') return;

  $current_template = get_post_meta($post_id, 'dw_regional_template', true);
  if (isset($_POST['page_template'])) {
    update_post_meta($post_id, 'dw_regional_template', $_POST['page_template']);
  }
  else if (empty($current_template)) {
    update_post_meta($post_id, 'dw_regional_template', 'page_generic_nav.php');
  }

}
add_action('save_post', 'dw_save_regional_template', 10, 3);
