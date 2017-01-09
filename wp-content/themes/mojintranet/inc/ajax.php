<?php
add_action('wp_ajax_get_autocomplete_items', 'ajax_get_autocomplete_items' );
add_action('wp_ajax_nopriv_get_autocomplete_items', 'ajax_get_autocomplete_items' );
function ajax_get_autocomplete_items(){
    $items = [];
    $post_type = $_POST['post_type'];
    $context = $_POST['context'];
    if(!empty($post_type) && !empty($context)) {
        $args = [ 'post_type' => $post_type, 'posts_per_page' => -1];
        $args['tax_query'] =  array(
            array(
                'taxonomy' => 'agency',
                'field'    => 'slug',
                'terms'    => $context,
            )
        );
        $posts = get_posts($args);
        foreach ($posts as $post) {
            $items[] = ['postid' => $post->ID, 'label' => $post->post_title];
        }
        echo json_encode($items);
    }
    die();
}
