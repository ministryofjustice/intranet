<?php

// Defines template editor options
// Template name doesn't need 'page-' prefix or '.php' suffix
$template_options = array(
    'guidance-and-support'  =>  array(
        'add' => array(),
        'del' => array('editor'),
        'metaboxes' => array(
            array(
                'id' => 'quick_links',
                'title' => 'Quick Links',
                'context' => 'normal',
                'priority' => 'core'
            ),
            array(
                'id' => 'content_tabs',
                'title' => 'Content Tabs',
                'context' => 'normal',
                'priority' => 'core'
            )
        ),
        'js' => array('jquery','jquery-ui-draggable','jquery-ui-tabs','jquery-ui-accordion'),
        'css' => array('jquery-admin-ui-css')
    )
);

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
    preg_match('/^page-(.+)\.php/',get_post_meta($post_id, '_wp_page_template', TRUE),$matches);
    $template_file = $matches[1];
    if (isset($template_options[$template_file])) {
        require(get_stylesheet_directory()."/inc/template-specific/".$template_file.".php");
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
    if(file_exists(get_stylesheet_directory().'/js/page-admin-'.$template_file.'.js')) {
        wp_register_script($template_file, get_stylesheet_directory_uri()."/js/page-admin-".$template_file.".js",$template_options[$template_file]['js']);
        wp_enqueue_script($template_file );
    }
    // Add template specific stylesheet
    if(file_exists(get_stylesheet_directory().'/css/page-admin-'.$template_file.'.css')) {
        wp_register_style($template_file, get_stylesheet_directory_uri()."/css/page-admin-".$template_file.".css",$template_options[$template_file]['css']);
        wp_enqueue_style($template_file );
    }
    if(isset($template_options[$template_file]['metaboxes'])) {
        foreach($template_options[$template_file]['metaboxes'] as $metabox) {
            add_action('save_post',$metabox['id'].'_save');
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
    preg_match('/^page-(.+)\.php/',get_post_meta($post_id, '_wp_page_template', TRUE),$matches);
    $template_file = $matches[1];

    // Add custom metaboxes for matching template
    if(isset($template_options[$template_file]['metaboxes'])) {
        foreach($template_options[$template_file]['metaboxes'] as $metabox) {
            add_meta_box(
                $metabox['id'],
                $metabox['title'],
                $metabox['id']."_callback",
                'page',
                $metabox['context'],
                $metabox['priority']
            );
        }
    }
}
add_action('add_meta_boxes_page','process_metaboxes');