<?php
use MOJ\Intranet\Agency;

function get_events_api() {
    
    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $siteurl = get_home_url();
    $post_per_page = 'per_page=10';
    $current_page = '&page=1';
    $agency_name = '&agency=' . $activeAgency['wp_tag_id'];
    
    
    
    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/event/?' . $post_per_page . $current_page . $agency_name . '&filter[orderby]=_event-start-date&order=desc'  );

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
            $event_start_date   = $post['_event-start-date'];
            $event_end_date     = $post['_event-end-date'];
            $start_date = date('d M Y', strtotime($event_start_date));
            $end_date = date('d M Y', strtotime($event_end_date));
            $get_year = date('Y', strtotime($event_start_date));
            $get_month = date('M', strtotime($event_start_date));
            $get_day = date('F', strtotime($event_start_date));
            $get_day_num = date('d', strtotime($event_start_date));

            $multiday = $event_start_date != $event_end_date;

            $start_time = $post['_event-start-time'];
            $end_time = $post['_event-end-time'];

            $location = $post['_event-location'];
            ?>
                <article class="c-events-item" data-type="event">  
                    <time class="c-calendar-icon" datetime="<?php echo $start_date . ' ' . $start_time; ?>">
                        <span class="c-calendar-icon--dow"><?php echo $get_day; ?></span>
                        <span class="c-calendar-icon--dom"><?php echo $get_day_num; ?></span>
                        <span class="c-calendar-icon--my"><?php echo $get_month . ' ' . $get_year; ?></span>
                    </time>
                    <h1><a href="<?php echo $post['link'];?>"><?php echo $post['title']['rendered'];?></a></h1>
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
add_action('wp_ajax_get_news_api', 'get_events_api');
add_action('wp_ajax_nopriv_get_news_api', 'get_events_api');
