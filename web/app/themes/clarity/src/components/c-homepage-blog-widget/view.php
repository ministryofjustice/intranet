<?php
use MOJ\Intranet\Agency;

$oAgency      = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

// TODO This block is required to check that featured items are not duplicated. Should be refactored.
$agency              = get_intranet_code();
$posttype_list_left  = get_field($agency . '_post_type_list', 'option');
$agencies_list_left  = get_field($agency . '_agency_list', 'option');
$posttype_list_right = get_field($agency . '_post_type_list_right', 'option');
$agencies_list_right = get_field($agency . '_agency_list_right', 'option');
$buildfeatleft       = $agency . '_feature_item_left_' . $posttype_list_left . '__' . $agencies_list_left;
$buildfeatright      = $agency . '_feature_item_right_' . $posttype_list_right . '__' . $agencies_list_right;
$feature_1           = get_field($buildfeatleft, 'option');
$feature_2           = get_field($buildfeatright, 'option');
$feature_1_id        = isset($feature_1->ID) ? $feature_1->ID : '';
$feature_2_id        = isset($feature_2->ID) ? $feature_2->ID : '';
$feature_array       = array( $feature_1_id, $feature_2_id );

$args = array(
    'post_type'      => 'post',
    'order'          => 'DESC',
    'orderby'        => 'date',
    'posts_per_page' => 3,
    // only show posts from active agency
    'tax_query'      => array(
        array(
            'taxonomy' => 'agency',
            'field'    => 'term_id',
            'terms'    => $activeAgency['wp_tag_id'],
        ),
    ),
    // exclude feature items by id
    'post__not_in'   => $feature_array,

);
// Standard Query Loop  - https://codex.wordpress.org/Class_Reference/WP_Query
$the_query = new WP_Query($args); ?>

<?php

if ($the_query->have_posts()) {
    echo '<h1 class="o-title o-title--section">Latest blogs</h1>';

    echo '<section class="c-homepage-blog-widget">';

    while ($the_query->have_posts()) {
        $the_query->the_post();
        get_template_part('src/components/c-blog-article-item/view');
    }
    echo '</section>';
    /* Restore original Post Data */
    wp_reset_postdata();
} else {
    // no posts found
}
?>

<a href="blog" class="o-see-all-link">See all blogs</a>
