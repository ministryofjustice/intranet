<?php

/* Dynamic filtering of Parent pages */

add_action('wp_ajax_check_parent', 'pageparent_ajax_check_parent');
function pageparent_ajax_check_parent() {
  global $wpdb;
  $query = $_POST['data'];
  $parent_query = "SELECT ID,post_title,post_parent,post_type,post_status FROM $wpdb->posts WHERE post_title LIKE '%{$query}%' AND post_type = 'page' AND post_status = 'publish' ORDER BY post_title LIMIT 0,30";
  $parentname = $wpdb->get_results($parent_query);
  if($parentname) {
    foreach ($parentname as $parent) {
      $parent_title = get_the_title($parent->post_parent);
      if ($parent_title!='') {
        $parent_title = $parent_title."&nbsp;>><br>";
      }
      $statecheck=get_post($parent->post_parent);
      $parent_state = get_the_title($statecheck->post_parent);
      if ($parent_state!='') {
        $parent_state = $parent_state."&nbsp;> ";
      }
      echo "<li class='pageparentoption'>
        <a class=\"parentlink\" style=\"cursor:pointer;\" parentname='".$parent->post_title . "' parentid='" . $parent->ID . "'>
          <small>".
            $parent_state." ".$parent_title."
          </small> ".
          $parent->post_title . "
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
  add_meta_box('pageparent-metabox', __('Parent Page'), 'pageparent_box', 'page', 'side', 'core');
}

function pageparent_box($post) {
  echo '<input type="hidden" name="taxonomy_noncename" id="taxonomy_noncename" value="' . wp_create_nonce( 'taxonomy_theme' ) . '"/>';

  //get current parent
  global $post;
  $load_image_url =  get_template_directory_uri() . '/admin/images/pageparent.gif';
  $parent_page = get_the_title(wp_get_post_parent_id($post->ID));

  //populate template list
  $current_template = get_post_meta($post->ID,'_wp_page_template',true);
  $template_file = str_replace('.php','',$current_template);
  $themeselect = '<select id="page_template" name="page_template">
          <option value="default">Default Template</option>';
  $templates = get_page_templates();
  foreach ( $templates as $template_name => $template_filename ) {
    $select = $current_template==$template_filename?'selected="selected"':"";
    $themeselect.= '<option value="'.$template_filename.'" '.$select.'>'.$template_name.'</option>';
  }
  $themeselect.= '</select>';

  ?>
  <p><strong>Current Template:</strong></p>
  <?php echo $themeselect;?>
  <p><strong>Current Parent:</strong></p>
  <div>
    <?php echo $parent_page; ?>
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
             'data'  : jQuery("#pageparent-filterbox").val()
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
