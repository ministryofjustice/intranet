<?php 

    function get_pagination($type) {

        $siteurl = get_home_url();
        $post_per_page = 'per_page=10';
        $current_page = '&page=1';
        
        $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/'.$type.'/?' . $post_per_page . $current_page );
        if( is_wp_error( $response ) ) {
        return;
        }

        $pagetotal = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );

        ?>

        <!-- c-pagination starts here -->
        <div id="load_more"></div>
        <nav class="c-pagination" role="navigation" aria-label="Pagination Navigation">
        <button class="more-btn" data-page="1" data-date="">            
            <span class="c-pagination__main "><span class="u-icon u-icon--circle-down"></span> Load Next 10 Results</span><span class="c-pagination__count"> 1 of <?php echo $pagetotal; ?></span>
            </button>    
        </nav>
        <!-- c-pagination ends here -->
  
<?php }