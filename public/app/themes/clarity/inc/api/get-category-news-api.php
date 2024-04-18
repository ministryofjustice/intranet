<?php
use MOJ\Intranet\Agency;

function get_category_news_api($category_id)
{
    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $post_per_page = 10;

    $args = [
        'numberposts' => $post_per_page,
        'post_type' => 'news',
        'post_status' => 'publish',
        'tax_query' => [
          'relation' => 'AND',
          [
            'taxonomy' => 'agency',
            'field' => 'term_id',
            'terms' => $activeAgency['wp_tag_id']
          ],
          // If the category_id is set add it to the taxonomy query
          ...( $category_id ? [
            'taxonomy' => 'news_category',
            'field' => 'category_id',
            'terms' =>  $category_id,
          ] : []),
      ]
    ];

    $posts = get_posts($args);
    echo '<div class="data-type" data-type="news"></div>';
    foreach ($posts as $key => $post) {
        $post_id = $post->ID;
        $link = get_the_permalink($post->ID);
        $author = $post->post_author;
        $author_avatar = $author ? get_the_author_meta('display_name', $author) : '';
        $author_display_name = $author ? get_the_author_meta('thumbnail_avatar', $author) : '';
        ?>
            <article class="c-article-item js-article-item" data-type="news">
                <?php $featured_img_url = wp_get_attachment_url(get_post_thumbnail_id($post_id)); ?>
                <?php
                if ($featured_img_url) {
                    ?>
                    <a href="<?php echo $link; ?>" class="thumbnail">
                        <img src="<?php echo $featured_img_url; ?>" alt="">
                    </a>
                    <?php
                } elseif ($author_avatar && $author_display_name) {
                    ?>
                    <a href="<?php echo $post['link']; ?>" class="thumbnail">
                        <img src="<?php echo $author_display_name ?>" alt="<?php echo $author_avatar ?>">
                    </a>
                    <?php
                } else {
                }
                ?>
                <div class="content">
                    <h1>
                        <a href="<?php echo $link; ?>"><?php echo $post->post_title; ?></a>
                    </h1>
                    <div class="meta">
                        <span class="c-article-item__dateline"><?php echo get_gmt_from_date($post->post_date, 'j M Y'); ?></span>
                    </div>
                    <div class="c-article-excerpt">
                        <p><?php echo $post->post_excerpt; ?></p>
                    </div>
                </div>
            </article>
        <?php
    }

add_action('wp_ajax_get_category_news_api', 'get_category_news_api');
add_action('wp_ajax_nopriv_get_category_news_api', 'get_category_news_api');
}