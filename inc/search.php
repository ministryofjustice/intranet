<?php
use MOJ\Intranet\Agency;

if (!defined('ABSPATH')) {
    die();
}

function load_events_filter_results(){
    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $siteurl = get_home_url();
    $agency_name = $activeAgency['wp_tag_id'];

    $datevalueselected = $_POST['valueSelected'];
    $query = $_POST['query'];
    
    $response = wp_remote_get( $siteurl.'/wp-json/intranet/v2/future-events/' . $query);

    if( is_wp_error( $response ) ) {
		return;
    }
    
    $pagetotal = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );
    
    $posts = json_decode( wp_remote_retrieve_body( $response ), true );

    $response_code       = wp_remote_retrieve_response_code( $response );
    $response_message = wp_remote_retrieve_response_message( $response );
    
    if ( 200 == $response_code && $response_message == 'OK' ) {
        echo '<div class="data-type" data-type="event"></div>';
        
		foreach( $posts as $key => $post ) {
            
            $event_start_date   = $post['event_start_date'];
            $event_end_date     = $post['event_end_date'];

            $strip_start_date  = substr($event_start_date, 0, 7);

            $start_date = date('d M Y', strtotime($event_start_date));
            $end_date = date('d M Y', strtotime($event_end_date));
            $get_year = date('Y', strtotime($event_start_date));
            $get_month = date('M', strtotime($event_start_date));
            $get_day = date('F', strtotime($event_start_date));
            $get_day_num = date('d', strtotime($event_start_date));
            $get_agency = $post['agency'][0]['term_id'];

            $post_title = $post['post_title'];

            $multiday = $event_start_date != $event_end_date;

            $start_time = $post['event_start_time'];
            $end_time = $post['event-end_time'];

            $location = $post['event_location'];
            
                if ($datevalueselected === $strip_start_date && $agency_name === $get_agency){
                    
                    ?>
                    <article class="c-events-item" data-type="event">  
                        <time class="c-calendar-icon" datetime="<?php echo $start_date . ' ' . $start_time; ?>">
                            <span class="c-calendar-icon--dow"><?php echo $get_day; ?></span>
                            <span class="c-calendar-icon--dom"><?php echo $get_day_num; ?></span>
                            <span class="c-calendar-icon--my"><?php echo $get_month . ' ' . $get_year; ?></span>
                        </time>
                        <h1><a href="<?php echo $post['url'];?>"><?php echo $post_title; ?></a></h1>
                        <div class="c-events-item__time">
                            <?php if($multiday): ?>
                                    <h2>Date: </h2>
                                    <time class="value"><?php echo $start_date . ' - ' . $end_date?></time>
                                <?php else: ?>
                                    <h2>Time: </h2>
                                    <time class="value"><?php echo $start_time . ' - ' . $end_time; ?></time>
                            <?php endif ?>
                        </div>    
                        <?php if (isset($location)) {?>
                            <div class="c-events-item__location">
                            <h2>Location:</h2>
                            <address><?php echo $location;?></address>
                            </div>
                        <?php } ?>
                        <span class="ie-clear"></span>
                    </article>
                    <?php
                }elseif($datevalueselected === 'all' && $agency_name === $get_agency ){
                    ?>
                    <article class="c-events-item" data-type="event">  
                        <time class="c-calendar-icon" datetime="<?php echo $start_date . ' ' . $start_time; ?>">
                            <span class="c-calendar-icon--dow"><?php echo $get_day; ?></span>
                            <span class="c-calendar-icon--dom"><?php echo $get_day_num; ?></span>
                            <span class="c-calendar-icon--my"><?php echo $get_month . ' ' . $get_year; ?></span>
                        </time>
                        <h1><a href="<?php echo $post['url'];?>"><?php echo $post_title; ?></a></h1>
                        <div class="c-events-item__time">
                            <?php if($multiday): ?>
                                    <h2>Date: </h2>
                                    <time class="value"><?php echo $start_date . ' - ' . $end_date?></time>
                                <?php else: ?>
                                    <h2>Time: </h2>
                                    <time class="value"><?php echo $start_time . ' - ' . $end_time; ?></time>
                            <?php endif ?>
                        </div>    
                        <?php if (isset($location)) {?>
                            <div class="c-events-item__location">
                            <h2>Location:</h2>
                            <address><?php echo $location;?></address>
                            </div>
                        <?php } ?>
                        <span class="ie-clear"></span>
                    </article>
                    <?php
                }           
                
        }
    }  
    die();
}
add_action('wp_ajax_load_events_filter_results', 'load_events_filter_results');
add_action('wp_ajax_nopriv_load_events_filter_results', 'load_events_filter_results');



function load_search_results(){

    $query = $_POST['query'];
    $valueSelected = $_POST['valueSelected'];
    $postType = $_POST['postType'];

    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $siteurl = get_home_url();
    $post_per_page = 'per_page=10';
    $search = (!empty($query) ? '&search=' . $query : '');
    //$search = '&search=' . $query;
    $agency_name = '&agency=' . $activeAgency['wp_tag_id'];

    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/'.$postType.'/?' . $post_per_page . $agency_name . $valueSelected . $search );

    $post_total = wp_remote_retrieve_header( $response, 'x-wp-total' );
    $posts = json_decode( wp_remote_retrieve_body( $response ), true );

    $response_code       = wp_remote_retrieve_response_code( $response );
	$response_message = wp_remote_retrieve_response_message( $response );

    if ( 200 == $response_code && $response_message == 'OK' ) {
        echo '<div class="data-type" data-type="'.$postType.'"></div>';
        foreach( $posts as $key => $post ) {
            ?>
            <article class="c-article-item js-article-item" class="<?php echo $postType ?>">
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
                        <span class="c-article-item__dateline"><?php echo get_gmt_from_date($post['date'], 'j M Y');?>
                        <?php if( $postType == 'posts' ){
                            echo 'by '. $post["coauthors"][0]["display_name"];
                        } ?>
                        </span>
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
    
    $search = (!empty($query) ? '&search=' . $query : '');
    $agency_name = '&agency=' . $activeAgency['wp_tag_id'];

    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/'.$postType.'/?' . $post_per_page . $current_page . $agency_name . $valueSelected . $search  );
    
    $pagetotal = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );

    $posts = json_decode( wp_remote_retrieve_body( $response ), true );

	$response_code       = wp_remote_retrieve_response_code( $response );
	$response_message = wp_remote_retrieve_response_message( $response );

    if ( 200 == $response_code && $response_message == 'OK' ) {
        echo '<div class="data-type" data-type="'.$postType.'"></div>';
        foreach( $posts as $key => $post ) {
            
                ?>
                <article class="c-article-item js-article-item" data-type="<?php echo $postType ?>">
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
                            <span class="c-article-item__dateline"><?php echo get_gmt_from_date($post['date'], 'j M Y');?> 
                            <?php if( $postType == 'posts' ){
                                echo 'by '. $post["coauthors"][0]["display_name"];
                            } ?>
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
    //$search = '&search=' . $query;
    $search = (!empty($query) ? '&search=' . $query : '');
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
    $onlyshow_todays_onwards = ($postType == 'event') ? '&order=asc&after='. current_time('Y-m-d h:i:s') : '';

    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/'.$postType.'/?' . $post_per_page . $current_page . $agency_name . $valueSelected . $onlyshow_todays_onwards . $search );

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