<?php

    $first_feature_item = get_field('first_featured_item');
    $second_feature_item = get_field('second_featured_item');

    $args = array(
        'post_type'       => 'team_specialists',
        'posts_per_page'  => 10,
        'post__not_in'    => array($first_feature_item, $second_feature_item)
    );

    $query = new WP_Query( $args );
    if ( $query->have_posts() ) {
      echo '<div class="c-news-list c-specialist-content">';
        while ( $query->have_posts() ) {
            $query->the_post();
            $news_link = get_permalink();
            $news_title = get_the_title();
            include(locate_template('src/components/c-article-item/view-teamlist.php'));
        } // end while
      echo '</div>';
    } // end if
    wp_reset_query();
?>
