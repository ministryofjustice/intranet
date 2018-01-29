<?php
use MOJ\Intranet\Agency;

if (!defined('ABSPATH')) {
    die();
}

function load_search_results(){

    $query = $_POST['query'];
    $valueSelected = $_POST['valueSelected'];
    $postType = $_POST['postType'];

    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $siteurl = get_home_url();
    $post_per_page = 'per_page=10';
    $search = '&search=' . $query;
    $agency_name = '&agency=' . $activeAgency['wp_tag_id'];

    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/'.$postType.'/?' . $post_per_page . $agency_name . $valueSelected . $search );

    $post_total = wp_remote_retrieve_header( $response, 'x-wp-total' );
    $posts = json_decode( wp_remote_retrieve_body( $response ), true );

    $response_code       = wp_remote_retrieve_response_code( $response );
	$response_message = wp_remote_retrieve_response_message( $response );

    if ( 200 != $response_code && ! empty( $response_message ) ) {
        
    } else {
        echo '<div class="data-type" data-type="'.$postType.'"></div>';
        foreach( $posts as $key => $post ) {

            ?>
                <article class="c-article-item js-article-item">
                    <?php $featured_img_url = wp_get_attachment_url( get_post_thumbnail_id($post['id']) ); ?>
                    <?php if( $featured_img_url ) {?>
                        <a href="<?php echo $post['link'] ?>" class="thumbnail">
                            <img src="<?php echo $featured_img_url?>" alt="">
                        </a>    
                    <?php }elseif (!empty($post['coauthors'][0]['thumbnail_avatar'])) { ?>
                        <a href="<?php echo $post['link'] ?>" class="thumbnail">
                            <img src="<?php echo $post['coauthors'][0]['thumbnail_avatar'] ;?>" alt="<?php echo $post['coauthors'][0]['display_name'] ;?>">
                        </a>
                    <?php }else{} ?> 
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
    $postType = $_POST['postType'];

    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

	$siteurl = get_home_url();
    $post_per_page = 'per_page=10';
    $current_page = '&page='. $nextPageToRetrieve;
    $search = '&search=' . $query;
    $agency_name = '&agency=' . $activeAgency['wp_tag_id'];

    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/'.$postType.'/?' . $post_per_page . $current_page . $agency_name . $valueSelected . $search );

    $pagetotal = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );

    $posts = json_decode( wp_remote_retrieve_body( $response ), true );

	$response_code       = wp_remote_retrieve_response_code( $response );
	$response_message = wp_remote_retrieve_response_message( $response );

    if ( 200 != $response_code && ! empty( $response_message ) ) {
        
    } else {
        echo '<div class="data-type" data-type="'.$postType.'"></div>';
        foreach( $posts as $key => $post ) {

            ?>
                <article class="c-article-item js-article-item">
                    <?php $featured_img_url = wp_get_attachment_url( get_post_thumbnail_id($post['id']) ); ?>
                    <?php if( $featured_img_url ) {?>
                        <a href="<?php echo $post['link'] ?>" class="thumbnail">
                            <img src="<?php echo $featured_img_url?>" alt="">
                        </a>    
                    <?php }elseif (!empty($post['coauthors'][0]['thumbnail_avatar'])) { ?>
                        <a href="<?php echo $post['link'] ?>" class="thumbnail">
                            <img src="<?php echo $post['coauthors'][0]['thumbnail_avatar'] ;?>" alt="<?php echo $post['coauthors'][0]['display_name'] ;?>">
                        </a>
                    <?php }else{} ?>
                        

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
    $postType = $_POST['postType'];

    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $siteurl = get_home_url();
    $post_per_page = 'per_page=10';
    $search = '&search=' . $query;
    $agency_name = '&agency=' . $activeAgency['wp_tag_id'];

    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/'.$postType.'/?' . $post_per_page . $agency_name . $valueSelected . $search );

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
    $postType = $_POST['postType'];

    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

	$siteurl = get_home_url();
    $post_per_page = 'per_page=10';
    $current_page = '&page='. $nextPageToRetrieve;
    $search = '&search=' . $query;
    $agency_name = '&agency=' . $activeAgency['wp_tag_id'];

    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/'.$postType.'/?' . $post_per_page . $current_page . $agency_name . $valueSelected.  $search );

    $pagetotal = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );

    $response_code          = wp_remote_retrieve_response_code( $response );
	$response_message       = wp_remote_retrieve_response_message( $response );

    if ( 200 != $response_code && ! empty( $response_message ) ) {
        
        echo '<span class="nomore-btn" data-date="'.$valueSelected.'">';
        echo '<span class="c-pagination__main">No Results</span>';
        echo '</span>';
    
    } else {
        if ($nextPageToRetrieve ==  $pagetotal){
            echo '<span class="nomore-btn" data-date="'.$valueSelected.'">';
            echo '<span class="c-pagination__main">No More Results</span>';
            echo '</span>';
        }elseif($pagetotal <= 1){
            echo '<button class="more-btn" data-page="'.$nextPageToRetrieve.'" data-date="'.$valueSelected.'">';
            echo '<span class="c-pagination__main">No More Results</span>';
            echo '<span class="c-pagination__count"> '.$nextPageToRetrieve . ' of 1</span>';
            echo '</button>'; 
        }else{
            echo '<button class="more-btn" data-page="'.$nextPageToRetrieve.'" data-date="'.$valueSelected.'">';
            echo '<span class="c-pagination__main"><span class="u-icon u-icon--circle-down"></span> Load Next 10 Results</span>';
            echo '<span class="c-pagination__count"> '.$nextPageToRetrieve . ' of ' . $pagetotal.'</span>';
            echo '</button>';
        }
    }
    
    die();
}
add_action('wp_ajax_load_page_total', 'load_page_total');
add_action('wp_ajax_nopriv_load_page_total', 'load_page_total');