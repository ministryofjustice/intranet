<?php 
use MOJ\Intranet\Agency;

    function get_pagination($type) {

        $oAgency = new Agency();
        $activeAgency = $oAgency->getCurrentAgency();

        $siteurl = get_home_url();
        $post_per_page = 'per_page=10';
        $current_page = '&page=1';
        $agency_name = '&agency=' . $activeAgency['wp_tag_id'];
        $onlyshow_todays_onwards = ($type == 'event') ? '&order=asc&after='. current_time('Y-m-d h:i:s') : '';
        
        $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/'.$type.'/?' . $post_per_page . $current_page . $agency_name . $onlyshow_todays_onwards );
        
        if( is_wp_error( $response ) ) {
            return;
        }

        $pagetotal = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );

        ?>

        <div id="load_more"></div>
        <nav class="c-pagination" role="navigation" aria-label="Pagination Navigation">
        <button class="more-btn" data-page="1" data-date="">            
            <span class="c-pagination__main "><span class="u-icon u-icon--circle-down"></span> Load Next 10 Results</span><span class="c-pagination__count"> 1 of <?php echo $pagetotal; ?></span>
            </button>    
        </nav>
  
<?php }