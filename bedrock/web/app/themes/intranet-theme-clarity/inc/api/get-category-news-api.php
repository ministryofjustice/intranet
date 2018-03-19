<?php
use MOJ\Intranet\Agency;

function get_category_news_api($category_id) {

    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $siteurl            = get_home_url();
    $post_per_page      = 'per_page=50';
    $current_page       = '&page=1';
    $agency_name        = '&agency=' . $activeAgency['wp_tag_id'];
    $category_name      = '&news_category=' .$category_id;

    $response = wp_remote_get( $siteurl.'/wp-json/wp/v2/news/?' . $post_per_page . $current_page . $agency_name . $category_name );

    if( is_wp_error( $response ) ) {
		return;
    }

    $pagetotal = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );

    $posts = json_decode( wp_remote_retrieve_body( $response ), true );

    $response_code       = wp_remote_retrieve_response_code( $response );
	$response_message = wp_remote_retrieve_response_message( $response );

    if ( 200 == $response_code && $response_message == 'OK' ) {
        echo '<div class="data-type" data-type="news"></div>';
		foreach( $posts as $key => $post ) {
            ?>
                <article class="c-article-item js-article-item" data-type="news">
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
                            <span class="c-article-item__dateline"><?php echo get_gmt_from_date($post['date'], 'j M Y');?></span>
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
add_action('wp_ajax_get_category_news_api', 'get_category_news_api');
add_action('wp_ajax_nopriv_get_category_news_api', 'get_category_news_api');
