<?php
use MOJ\Intranet\Teams;
use MOJ\Intranet\Authors;

$oAuthor = new Authors();
$oTeam   = new Teams();

// Set the number of blog posts
$number = 6;

$team_blog_posts = $oTeam->team_blog_api($number);

if ($team_blog_posts && $team_blog_posts !== 0) {
        echo '<div class="c-blog-feed">';
        echo '<h1 class="o-title o-title--section">Blog</h1>';

    foreach ($team_blog_posts as $team_blog_post) {
        $id            = $team_blog_post['id'] ?? '';
        $authors       = $oAuthor->getAuthorInfo($id);
        $thumbnail     = get_the_post_thumbnail_url($id, 'user-thumb');
        $thumbnail_alt = get_post_meta(get_post_thumbnail_id($id), '_wp_attachment_image_alt', true);
        ?>
            <article class="c-article-item js-article-item">

            <?php if ($thumbnail) : ?>
                <a href="<?php echo esc_url(get_permalink($id)); ?>">
                    <img src="
                    <?php echo esc_url($thumbnail); ?> " alt="<?php echo $thumbnail_alt; ?>">
                  </a>

              <div class="text-align">

            <?php elseif (! empty($authors[0]['thumbnail_url'])) : ?>
                <a href="<?php echo esc_url(get_permalink($id)); ?>">
                  <img src="
                  <?php
                    // If no feature image then show guest author image. .
                    echo $authors[0]['thumbnail_url'];
                    ?>
                " alt="<?php echo $authors[0]['thumbnail_alt_text']; ?>">
                </a>

              <div class="text-align">

            <?php else : ?>
                <div class="">
                    <?php // If no feature image or guest author image remove photo div and show nothing. ?>
                <!-- No author or blog image provided -->

            <?php endif; ?>

                <h1>
                  <a href="<?php echo esc_url(get_permalink($id)); ?>">
                    <?php echo get_the_title($id); ?>
                </a>
                </h1>

                <div class="meta">
                  <span class="c-article-item__dateline">
                    <?php echo get_the_time('j M Y', $id); ?>
                </span>
                </div>

              </div>

            </article>

            <?php
    }

        echo '</div>';
}
