<?php

if (!defined('ABSPATH')) {
    die();
}

function load_search_results(){

    $query = $_POST['query'];

    $valueSelected = $_POST['valueSelected'];

    $siteurl = get_home_url();
    $post_per_page = 'per_page=10';
    $search = '&search=' . $query;

    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/posts/?' . $post_per_page . $valueSelected . $search );

    $post_total = wp_remote_retrieve_header( $response, 'x-wp-total' );
    $posts = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( 200 != $response_code && ! empty( $response_message ) ) {
        
    } else {
    
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
add_action('wp_ajax_load_search_results', 'load_search_results');
add_action('wp_ajax_nopriv_load_search_results', 'load_search_results');

function load_next_results(){
    $nextPageToRetrieve = $_POST['nextPageToRetrieve'];
    $query = $_POST['query'];
    $valueSelected = $_POST['valueSelected'];

	$siteurl = get_home_url();
    $post_per_page = 'per_page=10';
    $current_page = '&page='. $nextPageToRetrieve;
    $search = '&search=' . $query;
    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/posts/?' . $post_per_page . $current_page . $valueSelected . $search );
    
    $pagetotal = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );

    $posts = json_decode( wp_remote_retrieve_body( $response ), true );

	$response_code       = wp_remote_retrieve_response_code( $response );
	$response_message = wp_remote_retrieve_response_message( $response );

    if ( 200 != $response_code && ! empty( $response_message ) ) {
        
    } else {
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
add_action('wp_ajax_load_next_results', 'load_next_results');
add_action('wp_ajax_nopriv_load_next_results', 'load_next_results');

function load_search_results_total ()
{
    $query = $_POST['query'];
    $valueSelected = $_POST['valueSelected'];
    $siteurl = get_home_url();
    $post_per_page = 'per_page=10';
    $search = '&search=' . $query;
    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/posts/?' . $post_per_page . $valueSelected . $search );

    $post_total = wp_remote_retrieve_header( $response, 'x-wp-total' );

    echo $post_total . ' search results';

    die();
    
}
add_action('wp_ajax_load_search_results_total', 'load_search_results_total');
add_action('wp_ajax_nopriv_load_search_results_total', 'load_search_results_total');

function load_page_total()
{
    $nextPageToRetrieve = $_POST['nextPageToRetrieve'];
    $query = $_POST['query'];
    $valueSelected = $_POST['valueSelected'];

	$siteurl = get_home_url();
    $post_per_page = 'per_page=10';
    $current_page = '&page='. $nextPageToRetrieve;
    $search = '&search=' . $query;
    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/posts/?' . $post_per_page . $current_page . $valueSelected.  $search );

    $pagetotal = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );

    $response_code          = wp_remote_retrieve_response_code( $response );
	$response_message       = wp_remote_retrieve_response_message( $response );

    if ( 200 != $response_code && ! empty( $response_message ) ) {
        
        echo '<span class="c-pagination__main">No more posts</span>';
    
    } else {
        if ($nextPageToRetrieve ==  $pagetotal){
            echo '<span class="c-pagination__main">No more posts</span>';
        }elseif($pagetotal <= 1){
            echo '<button class="more-btn" data-page="'.$nextPageToRetrieve.'" data-date="'.$valueSelected.'">';
            echo '<span class="c-pagination__main">No more posts</span>';
            echo '<span class="c-pagination__count"> '.$nextPageToRetrieve . ' of 1</span>';
            echo '</button>'; 
        }else{
            echo '<button class="more-btn" data-page="'.$nextPageToRetrieve.'" data-date="'.$valueSelected.'">';
            echo '<span class="c-pagination__main">Load More</span>';
            echo '<span class="c-pagination__count"> '.$nextPageToRetrieve . ' of ' . $pagetotal.'</span>';
            echo '</button>';
        }
    }
    
    die();
}
add_action('wp_ajax_load_page_total', 'load_page_total');
add_action('wp_ajax_nopriv_load_page_total', 'load_page_total');