<?php
if (!defined('ABSPATH')) {
    die();
}

$post_object = get_post($id);
$thumbnail_type = 'intranet-large';
$thumbnail_id = get_post_thumbnail_id($id);
$thumbnail = wp_get_attachment_image_src($thumbnail_id, $thumbnail_type);
$alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

if (isset($thumbnail)) {
    $thumbnail_url = $thumbnail[0];
}

?>
<!-- c-article-featured-image starts here -->
<?php if (has_post_thumbnail()) : ?>

  <div class="c-article-featured-image c-article-featured-image__news">
    <img src="<?php echo $thumbnail_url; ?>" alt="<?php echo $alt_text; ?>">
  </div>

<?php endif; ?>
<!-- c-article-featured-image ends here -->
