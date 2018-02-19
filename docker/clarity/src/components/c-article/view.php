<?php
use MOJ\Intranet\Authors;
$post = $data['post'];
$id = $post->ID;

$post_object = get_post($id);

$thumbnail_type = 'intranet-large';
$thumbnail_id = get_post_thumbnail_id($id);
$thumbnail = wp_get_attachment_image_src($thumbnail_id, $thumbnail_type);
$alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
$oAuthor = new Authors();
$authors = $oAuthor->getAuthorInfo($id);

?>
<!-- c-article starts here -->
<article class="c-article">
    <h1 class="o-title o-title--page"><a href="<?php echo get_the_permalink($id);?>"><?php echo get_the_title($id);?></a></h1>
    <?php get_component('c-article-byline'); ?>
    <?php get_component('c-rich-text-block'); ?>
    <?php get_component('c-share-post'); ?>
    <?php get_component('c-comment-form'); ?>
    <?php get_component('c-comments'); ?>
</article>
<!-- c-article ends here -->
