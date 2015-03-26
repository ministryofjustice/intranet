<?php
// Quick Links metabox
function quick_links_callback($post) {
    $ns = 'quick_links'; // Quick namespace variable
    $max_links = 7;
    wp_nonce_field( $ns.'_meta_box', $ns.'_meta_box_nonce' );

    // Populate link array
    $record_count = 0;
    $link_fields = array('link-text','url','qlink','firsttab','secondtab');
    $i=0;
    while($field_count<sizeof($link_fields)) {
        $i++;
        $field_count=0;
        foreach($link_fields as $link_field) {
            $link_field_transformed = str_replace('-','_',$link_field);
            if(metadata_exists( 'post', $post->ID, "_" . $ns . "-" . $link_field . $i )) {
                $$link_field_transformed = get_post_meta($post->ID, "_" . $ns . "-" . $link_field . $i,true);
            } else {
                $$link_field_transformed = null;
                $field_count++;
            }
        }
        if (($link_text || $link_url || $qlink || $firsttab || $secondtab) && $field_count<sizeof($link_fields)) {
            $record_count++;
            $link_array[$record_count] = array(
                'linktext' => $link_text,
                'linkurl' => $url,
                'qlink' => $qlink=='on'?' checked':'',
                'firsttab' => $firsttab=='on'?' checked':'',
                'secondtab' => $secondtab=='on'?' checked':''
            );
        }
    }
    $links_title = get_post_meta($post->ID, "_" . $ns . "-title",true);
    ?>
    <div class='<?=$ns?>-container' data-max-links='<?=$max_links?>'>
        <p><strong>Links Title (defaults to Links)</strong></p>
        <label class="screen-reader-text" for='<?=$ns?>-title'>Links Title (defaults to Links)</label>
        <input class='<?=$ns?>-title regular-text' id='<?=$ns?>-title' name='<?=$ns?>-title' type='text' value='<?=esc_attr($links_title)?>'>
        <table class=''>
            <thead>
                <tr>
                    <th scope="col" class="check-column"></th>
                    <th scope="col" class="">Link text</th>
                    <th scope="col" class="">Link URL</th>
                    <th scope="col" class="check-column">Quick Link</th>
                    <th scope="col" class="check-column">Tab&nbsp;1</th>
                    <th scope="col" class="check-column">Tab&nbsp;2</th>
                    <th scope="col" class=""></th>
                </tr>
            </thead>
            <tbody>
                <?php for($i=1;$i==1 || $i<=$record_count;$i++) { ?>
                <tr class='<?=$ns?>-line <?=$ns?>-line[<?=$i?>] draggable'>
                    <td>
                        <span class="dashicons dashicons-sort"></span>
                    </td>
                    <td>
                        <input class='<?=$ns?>-link-text <?=$ns?>-link-text<?=$i?> regular-text' id='<?=$ns?>-link-text<?=$i?>' name='<?=$ns?>-link-text<?=$i?>' type='text' placeholder='Link text' value='<?=esc_attr($link_array[$i]['linktext'])?>'>
                    </td>
                    <td>
                        <input class='<?=$ns?>-url <?=$ns?>-url<?=$i?> regular-text' id='<?=$ns?>-url<?=$i?>' name='<?=$ns?>-url<?=$i?>' type='text' placeholder='Link URL'  value='<?=esc_attr($link_array[$i]['linkurl'])?>'>
                    </td>
                    <td class="center">
                        <input class='<?=$ns?>-qlink <?=$ns?>-qlink<?=$i?>' type='checkbox' id='<?=$ns?>-qlink<?=$i?>' name='<?=$ns?>-qlink<?=$i?>'<?=$link_array[$i]['qlink']?>>
                    </td>
                    <td class="center">
                        <input class='<?=$ns?>-firsttab <?=$ns?>-firsttab<?=$i?>' type='checkbox' id='<?=$ns?>-firsttab<?=$i?>' name='<?=$ns?>-firsttab<?=$i?>'<?=$link_array[$i]['firsttab']?>>
                    </td>
                    <td class="center">
                        <input class='<?=$ns?>-secondtab <?=$ns?>-secondtab<?=$i?>' type='checkbox' id='<?=$ns?>-secondtab<?=$i?>' name='<?=$ns?>-secondtab<?=$i?>'<?=$link_array[$i]['secondtab']?>>
                    </td>
                    <td>
                        <a href='#' class='hide-if-no-js delete-link'>Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href='#' class='hide-if-no-js add-link'>+ Add link</a>
    </div>

    <p>You are allowed to enter up to <?=$max_links?> quick links</p>

    <?php
}
function quick_links_save($post_id) {
    $ns = 'quick_links'; // Quick namespace variable
    $search_content = null;
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
    $link_fields = array('link-text','url','qlink','firsttab','secondtab');
    $i=0;
    while($field_count<sizeof($link_fields)) {
        $i++;
        $field_count=0;
        foreach($link_fields as $link_field) {
            if (isset($_POST[$ns . "-" . $link_field . $i])) {
                $data = sanitize_text_field( $_POST[$ns . "-" . $link_field . $i] );
                update_post_meta( $post_id, "_" . $ns . "-" . $link_field . $i, $data );
                if($link_field=='link-text') {
                    $search_content .= $data."\r\n";
                }
            } else {
                delete_post_meta( $post_id, "_" . $ns . "-" . $link_field . $i);
                $field_count++;
            }
        }
    }

    update_post_meta( $post_id, "_" . $ns . "-title", sanitize_text_field( $_POST[$ns . "-title"] ));

    create_search_content('quicklinks_search',$search_content,$post_id);
}

// Content Tabs metabox
function content_tabs_callback($post) {
    if( file_exists( get_template_directory() . '/inc/js-wp-editor.php' ) ) {
        require_once( get_template_directory() . '/inc/js-wp-editor.php' );
        js_wp_editor();
    }
    $ns = 'content_tabs'; // Quick namespace variable
    wp_nonce_field( $ns.'_meta_box', $ns.'_meta_box_nonce' );

    $tab_count = get_post_meta( $post->ID, '_'.$ns.'-tab-count', true )!=null?get_post_meta( $post->ID, '_'.$ns.'-tab-count', true ):1;
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
            <?php
                for($tab=1;$tab<=$tab_count;$tab++) {
                    $tab_title = get_post_meta( $post->ID, '_'.$ns.'-tab-' . $tab . '-title', true )?:"Tab 1";
                    ?>
                <li><a href="#tabs-<?=$tab?>"><?=$tab_title?></a><a href='#' class='delete-tab'><span class="ui-icon ui-icon-close" role="presentation">Remove Tab</span></a></li>
            <?php } ?>
        </ul>
        <?php
            // Start tab
            for($tab=1;$tab<=$tab_count;$tab++) {
                $section_count = get_post_meta( $post->ID, '_'.$ns.'-tab-'.$tab.'-section-count', true );
                $section_count= $section_count?:1;
                $tab_title = get_post_meta( $post->ID, '_'.$ns.'-tab-' . $tab . '-title', true )?:"Tab ".$tab;
        ?>
        <div id="tabs-<?=$tab?>">
            <input type="hidden" id="tab-<?=$tab?>-section-count" name="tab-<?=$tab?>-section-count" value="<?=$section_count?>">
            <table class='form-table'>
                <tbody>
                    <tr class="form-field">
                        <th>
                            <label>Tab Title</label>
                        </th>
                        <td>
                            <input class="regular-text tab-title" id="tab-<?=$tab?>-title" name="tab-<?=$tab?>-title" type="text" value="<?=$tab_title?>">
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
                                <?php
                                    // Start section
                                    for($section=1;$section<=$section_count;$section++) {
                                        $section_title = get_post_meta($post->ID,'_'.$ns.'-tab-'.$tab.'-section-'.$section.'-title',true);
                                        $section_content = get_post_meta($post->ID,'_'.$ns.'-tab-'.$tab.'-section-'.$section.'-content',true);
                                    ?>
                                    <h3><?php echo $section_title?:"Section ".$section; ?></h3>
                                    <div>
                                        <table>
                                            <tbody>
                                                <tr class="form-field">
                                                    <th>
                                                        <label>Section Title</label>
                                                    </th>
                                                    <td>
                                                        <input type="text" id="tab-<?=$tab?>-section-<?=$section?>-title" name="tab-<?=$tab?>-section-<?=$section?>-title" value="<?=$section_title?>">
                                                    </td>
                                                </tr>
                                                <tr class="form-field">
                                                    <td colspan="2">
                                                        <?php wp_editor($section_content,'tab-' . $tab . '-section-' . $section . '-content'); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <a href="#" class="delete-section">Delete this section<span class="ui-icon ui-icon-close" role="presentation"></span></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php
                                    // End section
                                    }
                                ?>
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
    $search_content = null;
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
    if (isset($_POST["tab-count"])) {
        // Save tab count
        $data = sanitize_text_field( $_POST["tab-count"] );
        update_post_meta($post_id, "_".$ns."-tab-count",$data);
        $tab_count = $_POST['tab-count'];
        // Save tab data
        for($tab=1;$tab<=$tab_count;$tab++) {
            if (isset($_POST["tab-" . $tab . "-title"])) {
                $data = sanitize_text_field($_POST["tab-" . $tab . "-title"]);
                update_post_meta($post_id, "_".$ns."-tab-" . $tab . "-title",$data);
                $search_content .= $data."\r\n";
            }
            // Save section count
            $data = sanitize_text_field( $_POST["tab-".$tab."-section-count"] );
            update_post_meta($post_id, "_".$ns."-tab-".$tab."-section-count",$data);
            $section_count = $_POST["tab-".$tab."-section-count"];
            // Save section data
            $section_fields = array('title','content');
            for ($section=1;$section<=$section_count;$section++) {
                // echo $section."<br>";
                foreach($section_fields as $section_field) {
                    if (isset($_POST["tab-" . $tab . "-section-" . $section . "-".$section_field])) {
                        // echo $section_field . "set<br>";
                        $data = $_POST["tab-" . $tab . "-section-" . $section . "-".$section_field];
                        update_post_meta($post_id, "_".$ns."-tab-" . $tab . "-section-" . $section . "-".$section_field,$data);
                        $search_content .= $data."\r\n";
                        if($section_field=="content") {
                            $data = WPCom_Markdown::get_instance()->transform( $data );
                            update_post_meta($post_id, "_".$ns."-tab-" . $tab . "-section-" . $section . "-".$section_field . "-html",$data);
                            $search_content .= $data."\r\n";
                        }
                    }
                }
            }
        }
        create_search_content('tabs_search',$search_content,$post_id);
    }
}

// Hide preview button (temp solution until preview works properly)
global $pagenow;
if ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) {
    add_action( 'admin_head', 'dw_custom_publish_box' );
    function dw_custom_publish_box() {
        if( !is_admin() )
            return;

        $style = '';
        $style .= '<style type="text/css">';
        $style .= '#preview-action';
        $style .= '{display: none; }';
        $style .= '</style>';

        echo $style;
    }
}

// Force preview to work with custom meta - to be continued (TODO)!
/*
add_filter('_wp_post_revision_fields', 'dw_add_field_debug_preview');
function dw_add_field_debug_preview($fields){
    $ns = 'content_tabs'; // Quick namespace variable
    $tab_count = $_POST['tab-count'];
    $fields["_".$ns."tab-count"] = $tab_count;
    // for($tab=1;$tab<=$tab_count;$tab++) {
    //     $section_count = $_POST["tab-".$tab."-section-count"];
    //     for ($section=1;$section<=$section_count;$section++) {
    //         $fields["debug_preview"] = "debug_preview";
    //     }
    // }
    return $fields;
}
*/