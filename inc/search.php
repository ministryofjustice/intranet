<?php

if (!defined('ABSPATH')) {
    die();
}

/***
 *
 * Search engine related functions
 *
 ***/
add_action('wp_enqueue_scripts', 'ajax_search_enqueues');

function ajax_search_enqueues()
{
    wp_enqueue_script('ajax-search', get_stylesheet_directory_uri() . '/tests/js-test/blog-content_filter.js', array( ), '1.0.2', true);
    wp_localize_script('ajax-search', 'myAjax', array( 'ajaxurl' => admin_url('admin-ajax.php') ));
}

add_action('wp_ajax_load_search_results', 'load_search_results');
add_action('wp_ajax_nopriv_load_search_results', 'load_search_results');

function load_search_results()
{
    $query = $_POST['query'];

    $args = [
        'paged' => $paged,
        'posts_per_page' => 5,
        'post_type' => 'post',
        'post_status' => 'publish',

        's' => $query
    ];
    $search = new WP_Query($args);

    $prev_page_number = $paged-1;
    $next_page_number = $paged+1;

    $total_page_number = $query->max_num_pages;

    ob_start();

    if ($search->have_posts()) :

    ?>

		<?php
            while ($search->have_posts()) : $search->the_post();
    get_component('c-article-item', '', 'show_excerpt');
    endwhile; ?>
            <nav class="c-pagination" role="navigation" aria-label="Pagination Navigation">
                <?php
                    echo previous_posts_link('<span class="c-pagination__main">Previous page</span><span class="c-pagination__count">'.$prev_page_number.' of '.$total_page_number.'</span>');
    echo next_posts_link('<span class="c-pagination__main">Next page</span><span class="c-pagination__count">'.$next_page_number.' of '.$total_page_number.'</span>', $total_page_number); ?>
            </nav>
            <?php
    else :
        echo 'nothing';
    endif;

    $content = ob_get_clean();

    echo $content;
    die();
}
