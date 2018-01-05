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
    wp_enqueue_script('ajax-search', get_stylesheet_directory_uri() . '/tests/js-test/blog-content_filter.js', array( ), '1.2.22', true);
    wp_localize_script('ajax-search', 'myAjax', 
        array( 'ajaxurl' => admin_url('admin-ajax.php') )
    );
}

add_action('wp_ajax_load_search_results', 'load_search_results');
add_action('wp_ajax_nopriv_load_search_results', 'load_search_results');



function load_search_results()
{
    $nextPageToRetrieve = $_POST['nextPageToRetrieve'];
	$siteurl = get_home_url();
    $post_per_page = 'per_page=5';
    $current_page = '&page='. $nextPageToRetrieve;
    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/posts/?' . $post_per_page . $current_page );
    if( is_wp_error( $response ) ) {
		return;
    }
    
    $pagetotal = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );
    
    $posts = json_decode( wp_remote_retrieve_body( $response ), true );

	if( empty( $posts ) ) {
		return;
    }
    
    if( !empty( $posts ) ) {
		foreach( $posts as $key => $post ) {

            ?>
                <article class="c-article-item js-article-item">
                    <a href="<?php echo $post['link'] ?>" class="thumbnail">
                        <img src="<?php echo $post['coauthors'][0]['thumbnail_avatar'] ;?>" alt="<?php echo $post['coauthors'][0]['display_name'] ;?>">
                    </a>
                    <div class="content">
                        <h1>
                            <a href="<?php echo $post['link'] ?>"><?php echo $post['title']['rendered']?></a>
                        </h1>
                        <div class="meta">
                            <span class="c-article-item__dateline"><?php echo get_gmt_from_date($post['date'], 'j M Y');;?> by <?php echo $post["coauthors"][0]["display_name"] ?></span>
                        </div>    
                        <div class="c-article-exceprt">
                            <p><?php echo $post['excerpt']['rendered'] ?></p>
                        </div>
                    </div>        
                </article>
            <?php
		}
    }
    die();
}
