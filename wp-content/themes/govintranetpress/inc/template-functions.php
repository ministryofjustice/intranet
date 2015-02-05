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
add_action('init', 'template_customise');

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

// Quick Links metabox
function quick_links_callback($post) {
    $ns = 'quick_links'; // Quick namespace variable
    wp_nonce_field( $ns.'_meta_box', $ns.'_meta_box_nonce' );

    // Populate link array
    $record_count = 0;
    for($i=1;$i<=5;$i++) { 
        $link_text = get_post_meta($post->ID, "_" . $ns . "-link-text" . $i,true);
        $link_url = get_post_meta($post->ID, "_" . $ns . "-url" . $i,true);
        if ($link_text!=null || $link_url!=null) {
            $record_count++;
            $link_array[$record_count] = array(
                'linktext' => $link_text,
                'linkurl' => $link_url
            );
        }
    }
    ?>
    <div class='<?=$ns?>-container'>
        <table class='form-table'>
            <tbody>
                <?php for($i=1;$i<=$record_count;$i++) { ?>
                <tr class='<?=$ns?>-line <?=$ns?>-line[<?=$i?>] draggable'>
                    <!--<td>
                        <span class="dashicons dashicons-sort"></span>
                    </td>-->
                    <td>
                        <input class='<?=$ns?>-link-text <?=$ns?>-link-text<?=$i?> regular-text' id='<?=$ns?>-link-text<?=$i?>' name='<?=$ns?>-link-text<?=$i?>' type='text' placeholder='Link text' value='<?=esc_attr($link_array[$i]['linktext'])?>'>
                    </td>
                    <td>
                        <input class='<?=$ns?>-url <?=$ns?>=url<?=$i?> regular-text' id='<?=$ns?>-url<?=$i?>' name='<?=$ns?>-url<?=$i?>' type='url' placeholder='Link URL'  value='<?=esc_attr($link_array[$i]['linkurl'])?>'>
                    </td>
                    <td>
                        <a href='#' class='hide-if-no-js delete-link'>Delete</a>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($record_count<5) { ?>
                <tr>
                    <th scope='row' valign='top'>
                        <a href='#' class='hide-if-no-js add-link'>+ Add link</a>
                    </th>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <p>You are allowed to enter up to 5 quick links</p>

    <?php
}
function quick_links_save($post_id) {
    $ns = 'quick_links'; // Quick namespace variable
    if ( ! isset( $_POST[$ns.'_meta_box_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST[$ns.'_meta_box_nonce'], $ns.'_meta_box' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }
    // Debug::full($_POST);
    $link_fields = array('link-text','url');
    for($i=1; $i<=5; $i++) {
        foreach($link_fields as $link_field) {
            if (isset($_POST[$ns . "-" . $link_field . $i])) {
                $data = sanitize_text_field( $_POST[$ns . "-" . $link_field . $i] );
                update_post_meta( $post_id, "_" . $ns . "-" . $link_field . $i, $data );
            } else {
                delete_post_meta( $post_id, "_" . $ns . "-" . $link_field . $i);
            }
        }
    }
}

// Content Tabs metabox
function content_tabs_callback($post) {
    js_wp_editor();
    $ns = 'content_tabs'; // Quick namespace variable
    wp_nonce_field( $ns.'_meta_box', $ns.'_meta_box_nonce' );

    $tab_count = 1;
    ?>
    <input type="hidden" id="tab-count" name="tab-count" value="<?=$tab_count?>">
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row" valign="top">
                    <a class="hide-if-no-js add-tab" href="#">+ Add Tab</a>
                </th>
            </tr>
        </tbody>
    </table>
    <div class='<?=$ns?>-container tabs'>
        <ul>
            <?php for($tab=1;$tab<=$tab_count;$tab++) { ?>
                <li><a href="#tabs-<?=$tab?>">Tab 1</a><span class="ui-icon ui-icon-close" role="presentation">Remove Tab</span></li>
            <?php } ?>
        </ul>
        <?php // Start tab ?>
        <?php for($tab=1;$tab<=$tab_count;$tab++) { ?>
        <div id="tabs-1">
            <table class='form-table'>
                <tbody>
                    <tr class="form-field">
                        <th>
                            <label>Tab Title</label>
                        </th>
                        <td>
                            <input class="regular-text tab-title" id="tab-<?=$tab?>-title" name="tab-<?=$tab?>-title" type="text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" valign="top" colspan="2">
                            <a class="hide-if-no-js add-section" href="#">+ Add Section</a>
                        </th>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="accordion">
                                <?php // Start section ?>
                                <h3>Section 1</h3>
                                <div>
                                    <table>
                                        <tbody>
                                            <tr class="form-field">
                                                <th>
                                                    <label>Section Title</label>
                                                </th>
                                                <td>
                                                    <input type="text">
                                                </td>
                                            </tr>
                                            <tr class="form-field">
                                                <td colspan="2">
                                                    <?php wp_editor(null,'tab-1-section-1-content'); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <?php // End section ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php } ?>
        <?php // End tab ?>
    </div>
    <?php
}
function content_tabs_save($post_id) {
    $ns = 'content_tabs'; // Quick namespace variable
    if ( ! isset( $_POST[$ns.'_meta_box_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST[$ns.'_meta_box_nonce'], $ns.'_meta_box' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }
    $tab_count = $_POST['tab-count'];
    for($tab=1;$tab<=$tab_count;$tab++) {
        if (isset($_POST["tab-" . $tab . "-title"])) {
            $data = sanitize_text_field($_POST["tab-" . $tab . "-title"]);
            update_post_meta($post_id, "_".$ns."-tab-title".$tab,$data);
        }
    }
}