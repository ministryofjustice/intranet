<?php
/*
* Template Name: Media grid template
* Template Post Type: page, regional_page
*/
if (!defined('ABSPATH')) {
    die();
}

$post_id   = get_the_ID();
$region_id = get_the_terms($post_id, 'region');

get_header();

?>
<main role="main" id="maincontent" class="u-wrapper l-main t-media-grid">

    <?php
    if (is_singular('regional_page') && $region_id) :
        get_template_part('src/components/c-breadcrumbs/view', 'region-single');
    else :
        get_template_part('src/components/c-breadcrumbs/view');
    endif;
    ?>

    <?php get_template_part('src/components/c-full-width-banner/view', 'team'); ?>

  <h1 class="o-title o-title--page"><?php echo the_title(); ?></h1>

    <div class="l-full-page" role="main">

        <?php
      // check if the flexible content field has rows of data

        if (have_rows('content_section')) :
          // loop through the rows of data
            while (have_rows('content_section')) :
                the_row();

                if (get_row_layout() == 'body_text_block') :
                    echo '<section class="c-rich-text-block">';
                    the_sub_field('body_text');
                    echo '</section>';
                elseif (get_row_layout() == 'feature_media_block') :
                    $feature_image = get_sub_field('feature_image');
                    $feature_text = get_sub_field('feature_text');
                    include(locate_template('src/components/c-feature-media/view.php'));
                elseif (get_row_layout() == 'feature_video_block') :
                  // check if the nested repeater field has rows of data
                    if (have_rows('feature_video_gallery')) :
                        echo '<div class="video-container">';
                          // loop through the rows of data
                        while (have_rows('feature_video_gallery')) :
                            the_row();

                            $youtube_id = get_sub_field('feature_video');

                            include(locate_template('src/components/c-gallery-lightbox/view.php'));
                        endwhile;

                        echo '</div>';
                    endif;
                elseif (get_row_layout() == 'feature_image_block') :
                    $images = get_sub_field('feature_image_gallery');

                    if ($images) : ?>
                    <div class="image-gallery-container">
                            <?php foreach ($images as $image) : ?>
                                <?php
                                $gallery_image = $image['url'];
                                $gallery_image_id = $image['id'];
                                $size = 'medium';
                                include(locate_template('src/components/c-gallery-lightbox/view-image.php'));
                                ?>
                            <?php endforeach; ?>
                    </div>
                    <?php endif;
                elseif (get_row_layout() == 'quote_block') :
                  // check if the nested repeater field has rows of data
                    if (have_rows('quotes')) :
                        echo '<div class="quote-container">';
                          // loop through the rows of data
                        while (have_rows('quotes')) :
                            the_row();

                            $quote_text = get_sub_field('quote_text');
                            $quote_author = get_sub_field('quote_author');

                            include(locate_template('src/components/c-quotes/view.php'));
                        endwhile;

                        echo '</div>';
                    endif;
                endif;
            endwhile;
        else :
          // no layouts found
        endif;
        ?>

    </div>

  <section class="l-full-page">
    <?php get_template_part('src/components/c-last-updated/view'); ?>
    <?php get_template_part('src/components/c-share-post/view'); ?>
    <?php get_template_part('src/components/c-comments/view'); ?>
  </section>

</main>

<?php
get_footer();
