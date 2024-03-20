<?php

/**
 * Hide templates from editors
 * Two step process
 */

$templates_we_want_to_hide = [
    'page_agency_switcher.php',
    'page_region_directory.php',
    'page_regional_archive_events.php',
    'page_regional_archive_news.php',
    'page_regional_landing.php',
    'page_team_homepage.php',
    'page_news.php',
    'page_news-tibit.php',
    'page_news-oneupdate.php',
    'page_news-enews.php',
    'page_home.php',
    'page_guidance_and_support_index.php',
    'page_events.php',
    'page_blog.php',
    'page_about_us.php',
    'search.php',
];

/**
 * 1. Step one
 * Hide/remove templates from the dropdown Page Attribute box.
 * Some templates are only used once for particular pages, ie homepage, landingpages,
 * so it doesn't make sense for editors to have these at their disposal as they can't use them for anything else.
 * Only admins can see all templates.
 */

if (! current_user_can('administrator')) {
    add_action('admin_footer', 'clarity_hide_templates_in_dropdown', 10);

    function clarity_hide_templates_in_dropdown()
    {

        global $pagenow;
        global $templates_we_want_to_hide;

        // convert PHP var into JS
        $templates_we_want_to_hide_encoded = json_encode((array) $templates_we_want_to_hide);

        if (in_array($pagenow, array( 'post-new.php', 'post.php' ))) { ?>
            <script type="text/javascript">

                    var template_array = <?php echo $templates_we_want_to_hide_encoded; ?>;

                    (function($){
                            $(document).ready(function(){
                                for ( i=0; i < template_array.length; i++) {
                                    $('#page_template option[value="' + template_array[i] + '"]').remove();
                                }
                            })
                    })(jQuery)
                    
            </script>

            <?php
        }
    }
}


/**
 * Step two
 * Once the templates are removed from the dropdown lists (above),
 * What do we do when an editor goes to one of the pages where the template has been removed?
 * It is replaced with a message that indicates page can't be changed (see below).
 */

if (! current_user_can('administrator')) {
    add_action('admin_footer', 'clarity_hide_replace_template_dropdown_input', 10);

    function clarity_hide_replace_template_dropdown_input($post)
    {

        global $post;
        global $templates_we_want_to_hide;

        if (!is_object($post)) {
            return;
        }
        
        $current_template = get_post_meta($post->ID, '_wp_page_template', true);

        if (in_array($current_template, $templates_we_want_to_hide)) {
            ?>
                <script type="text/javascript">
                        (function($){
                                $(document).ready(function(){
                                    $('#page_template').replaceWith( "<p>Page template is unique to this page and cannot be changed.</p>" );
                                })
                        })(jQuery)
                </script>
            <?php
        }
    }
}



