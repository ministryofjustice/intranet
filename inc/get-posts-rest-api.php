<?php 

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
        //print_r($coauthors);
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
    
    $posts = json_decode( wp_remote_retrieve_body( $response ) );

    echo '<pre>';
    //print_r($posts);
    echo '</pre>';

	if( empty( $posts ) ) {
		return;
    }
    
    if( !empty( $posts ) ) {
		foreach( $posts as $post ) {
            ?>
                <article class="c-article-item js-article-item">
                    <h1>
                        <a href="<?php echo $post->link ?>"><?php echo $post->title->rendered?></a>
                    </h1>
                    <div class="c-article-exceprt">
                        <p><?php echo $post->excerpt->rendered ?></p>
                    </div>
                    
                </article>
            <?php
		}
	}
    


}