<?php
use MOJ\Intranet\Agency;
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
    
    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $siteurl = get_home_url();
    $post_per_page = 'per_page=10';
    $current_page = '&page=1';
    $agency_name = '&agency=' . $activeAgency['wp_tag_id'];
    
    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/posts/?' . $post_per_page . $current_page . $agency_name );

    
    if( is_wp_error( $response ) ) {
		return;
    }
    
    $pagetotal = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );
    
    $posts = json_decode( wp_remote_retrieve_body( $response ), true );

    $response_code       = wp_remote_retrieve_response_code( $response );
	$response_message = wp_remote_retrieve_response_message( $response );

    if ( 200 == $response_code && $response_message == 'OK' ) {
        echo '<div class="data-type" data-type="posts"></div>';
        
		foreach( $posts as $key => $post ) {

            ?>
                <article class="c-article-item js-article-item" >
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
    
}
add_action('wp_ajax_get_post_api', 'get_post_api');
add_action('wp_ajax_nopriv_get_post_api', 'get_post_api');