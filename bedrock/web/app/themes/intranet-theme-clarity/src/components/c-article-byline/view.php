<?php
use MOJ\Intranet\Authors;

if (!defined('ABSPATH')) {
    die();
}

$post_object = get_post($id);
$thumbnail_type = 'intranet-large';
$thumbnail_id = get_post_thumbnail_id($id);
$thumbnail = wp_get_attachment_image_src($thumbnail_id, $thumbnail_type);

$oAuthor = new Authors();
$authors = $oAuthor->getAuthorInfo($id);
$thumbnail_url = $thumbnail[0];

if (isset($authors[0]['name'])){
  $author_name = $authors[0]['name'];
}else{
  $author_name = the_author();
}


?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<!-- c-article-byline starts here -->
<section class="c-article-byline">
  <img class="c-article-byline__photo" src="<?php echo $authors[0]['thumbnail_url']; ?>" alt="Photo of author, <?php echo $author_name ?>">
  <span class="c-article-byline__intro"><?php echo $author_name; ?></span>
  <span class="c-article-byline__date"><?php the_date('d F Y'); ?></span>
</section>
<!-- c-article-byline ends here -->
<?php endwhile; else : ?>
	<p><?php esc_html_e('Sorry, nothing was found.'); ?></p>
<?php endif; ?>
