<?php
use MOJ\Intranet\Agency;

function get_events_api($taxonomy, $tax_id = false) {

    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $siteurl = get_home_url();
    $agency_name = $activeAgency['wp_tag_id'];


    if ($taxonomy === 'search'){
        $response = wp_remote_get( $siteurl.'/wp-json/intranet/v2/future-events/'.$agency_name);
    }elseif($taxonomy === 'region'){
        $response = wp_remote_get( $siteurl.'/wp-json/intranet/v2/region-events/'.$agency_name.'/'.$tax_id.'/');
    }elseif($taxonomy === 'campaign'){
        $response = wp_remote_get( $siteurl.'/wp-json/intranet/v2/campaign-events/'.$agency_name.'/'.$tax_id.'/');
    }

    if( is_wp_error( $response ) ) {
		return;
    }

    $pagetotal = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );

    $posts = json_decode( wp_remote_retrieve_body( $response ), true );

    $response_code       = wp_remote_retrieve_response_code( $response );
    $response_message = wp_remote_retrieve_response_message( $response );

    if ( 200 == $response_code && $response_message == 'OK' ) {
        echo '<div class="data-type" data-type="event"></div>';

		foreach( $posts['events'] as $key => $post ) {

            $event_start_date   = $post['event_start_date'];
            $event_end_date     = $post['event_end_date'];
            $start_date = date('d M Y', strtotime($event_start_date));
            $end_date = date('d M Y', strtotime($event_end_date));
            $get_year = date('Y', strtotime($event_start_date));
            $get_month = date('M', strtotime($event_start_date));
            $get_day = date('F', strtotime($event_start_date));
            $get_day_num = date('d', strtotime($event_start_date));

            $multiday = $event_start_date != $event_end_date;

            $start_time = $post['event_start_time'];
            $end_time = $post['event_end_time'];

            $location = $post['event_location'];

            ?>
                <article class="c-events-item" data-type="event">
                    <?php include(locate_template('src/components/c-calendar-icon/view-events-api.php')); ?>
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
add_action('wp_ajax_get_news_api', 'get_events_api');
add_action('wp_ajax_nopriv_get_news_api', 'get_events_api');
