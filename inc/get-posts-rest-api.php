<?php 
// this is a plugin 'co authors plus', as standard it's not included into the core WP api, this function includes it
if ( function_exists('get_coauthors') ) {
    add_action( 'rest_api_init', 'custom_register_coauthors' );
    function custom_register_coauthors() {
        register_rest_field( 'post',
            'coauthors',
            array(
                'get_callback'    => 'custom_get_coauthors',
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }
 
    function custom_get_coauthors( $object, $field_name, $request ) {
        $coauthors = get_coauthors($object['id']);
 
        $authors = array();
        foreach ($coauthors as $author) {
            $authors[] = array(
                'display_name' => $author->display_name,
                'author_id' => $author->ID,
                'thumbnail_avatar' => get_the_post_thumbnail_url( $author->ID, 'intranet-large' ),
            );
        };
 
        return $authors;
    }
}

function get_post_api() {

    $siteurl = get_home_url();
    $post_per_page = 'per_page=5';
    $current_page = '&page=1';
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
        echo '<div id="load_more"></div>';
        echo '<nav class="c-pagination" role="navigation" aria-label="Pagination Navigation">';
        echo '<a href="#" class="more-btn"><span class="c-pagination__main">Next page</span><span class="c-pagination__count"> 1 of '.$pagetotal.'</span></a>';
        echo '</nav>';
	}
    
}