<?php

// Grab the config array
require_once(get_stylesheet_directory()."/admin/templates/template-config.php");

function template_customise() {
    global $template_options;

    // if post not set, just return
    // fix when post not set, throws PHP's undefined index warning
    if (isset($_GET['post'])) {
        $post_id = $_GET['post'];
    } else if (isset($_POST['post_ID'])) {
        $post_id = $_POST['post_ID'];
    } else {
        return;
    }
    $page_template = str_replace('_','-',get_post_meta($post_id, '_wp_page_template', TRUE));
    preg_match('/^page-(.+)\.php/',$page_template,$matches);
    $template_file = $matches[1]?:("single-".get_post_type( $post_id ));
    if (isset($template_options[$template_file])) {
        $template_include = get_stylesheet_directory()."/admin/templates/template-specific/".$template_file.".php";
        if(file_exists($template_include)) {
          require($template_include);
        }
        // Add post type support for matching template
        if(isset($template_options[$template_file]['add'])) {
            add_post_type_support('page', $template_options[$template_file]['add'] );
        }
        // Remove post type support for matching template
        if(isset($template_options[$template_file]['del'])) {
            // remove_post_type_support moronically doesn't support feature array
            // have to iterate through del feature values and call remove_post_type_support multiple times
            foreach($template_options[$template_file]['del'] as $del_feature) {
                remove_post_type_support('page', $del_feature );
            }
        }
    }
    // Add template specific javascript file
    $template_js = get_stylesheet_directory().'/admin/templates/template-specific/page-admin-'.$template_file.'.js';
    if(file_exists($template_js)) {
        wp_register_script($template_file, get_stylesheet_directory_uri()."/admin/templates/template-specific/page-admin-".$template_file.".js",$template_options[$template_file]['js'],filemtime($template_js));
        wp_enqueue_script($template_file );
    }
    // Add template specific stylesheet
    $template_css = get_stylesheet_directory().'/admin/templates/template-specific/page-admin-'.$template_file.'.css';
    if(file_exists($template_css)) {
        wp_register_style($template_file, get_stylesheet_directory_uri()."/admin/templates/template-specific/page-admin-".$template_file.".css",$template_options[$template_file]['css'],filemtime($template_css));
        wp_enqueue_style($template_file );
    }
    if(isset($template_options[$template_file]['metaboxes'])) {
        foreach($template_options[$template_file]['metaboxes'] as $metabox) {
            add_action('save_post',$metabox['id'].'_save',5);
        }
    }
}
add_action('init', 'template_customise',110);

function process_metaboxes() {
    global $template_options;

    if (isset($_GET['post'])) {
        $post_id = $_GET['post'];
    } else if (isset($_POST['post_ID'])) {
        $post_id = $_POST['post_ID'];
    } else {
        return;
    }

    $page_template = str_replace('_','-',get_post_meta($post_id, '_wp_page_template', TRUE));
    preg_match('/^page-(.+)\.php/',$page_template,$matches);
    if($matches[1]) {
      $template_file = $matches[1];
      $post_type = 'page';
    } else {
      $post_type = get_post_type( $post_id );
      $template_file = "single-".$post_type;
    }
    // Add custom metaboxes for matching template
    if(isset($template_options[$template_file]['metaboxes'])) {
        foreach($template_options[$template_file]['metaboxes'] as $metabox) {
            add_meta_box(
                $metabox['id'],
                $metabox['title'],
                $metabox['id']."_callback",
                $post_type,
                $metabox['context'],
                $metabox['priority']
            );
        }
    }
}
add_action('add_meta_boxes','process_metaboxes');
