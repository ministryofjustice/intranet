<?php
use MOJ\Intranet\Agency;

function get_events_api() {
    
    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $siteurl = get_home_url();
    $agency_name = $activeAgency['wp_tag_id'];
    
    $response = wp_remote_get( $siteurl.'/wp-json/intranet/v2/future-events/?');

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
            $start_date = date('d M Y', strtotime($event_start_date));
            $end_date = date('d M Y', strtotime($event_end_date));
            $get_year = date('Y', strtotime($event_start_date));
            $get_month = date('M', strtotime($event_start_date));
            $get_day = date('F', strtotime($event_start_date));
            $get_day_num = date('d', strtotime($event_start_date));
            $get_agency = $post['agency'][0]['term_id'];

            $multiday = $event_start_date != $event_end_date;

            $start_time = $post['event_start_time'];
            $end_time = $post['event_end_time'];

            $location = $post['event_location'];
            
            if ($agency_name === $get_agency){
                ?>
                <article class="c-events-item" data-type="event">  
                    <time class="c-calendar-icon" datetime="<?php echo $start_date . ' ' . $start_time; ?>">
                        <span class="c-calendar-icon--dow"><?php echo $get_day; ?></span>
                        <span class="c-calendar-icon--dom"><?php echo $get_day_num; ?></span>
                        <span class="c-calendar-icon--my"><?php echo $get_month . ' ' . $get_year; ?></span>
                    </time>
                    <h1><a href="<?php echo $post['url'];?>"><?php echo $post['post_title'];?></a></h1>
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
     
}
add_action('wp_ajax_get_news_api', 'get_events_api');
add_action('wp_ajax_nopriv_get_news_api', 'get_events_api');
