<?php
add_action('wp_ajax_get_autocomplete_items', 'ajax_get_autocomplete_items' );
add_action('wp_ajax_nopriv_get_autocomplete_items', 'ajax_get_autocomplete_items' );
function ajax_get_autocomplete_items(){
    global $wpdb;
    $items = [];
    $post_type = $_GET['post_type'];
    $context = $_GET['context'];
    $search_term = $_GET['search_term'];
    if(!empty($post_type) && !empty($context) && !empty($search_term)) {

        $posts_query = "SELECT ID, post_title FROM $wpdb->posts
                   LEFT JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id )
                   LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id )
                   LEFT JOIN $wpdb->terms ON ( $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id )
                   WHERE post_title LIKE '%%%s%%'
                   AND post_type = '%s'
                   AND post_status IN ('publish')
                   AND $wpdb->term_taxonomy.taxonomy = 'agency'
                   AND $wpdb->terms.slug IN ('%s')
                   GROUP BY $wpdb->posts.ID
                   ORDER BY post_modified DESC LIMIT 0,30
                 ";

        $posts = $wpdb->get_results($wpdb->prepare($posts_query, array($search_term, $post_type, $context)));

        foreach ($posts as $post) {
            $items[] = ['postid' => $post->ID, 'label' => $post->post_title];
        }
        echo json_encode($items);
    }
    die();
}
