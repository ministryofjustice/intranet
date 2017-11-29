<?php
use MOJ\Intranet\Authors;

$id = $data['id'];

$post_object = get_post($id);

$thumbnail_type = 'intranet-large';
$thumbnail_id = get_post_thumbnail_id($id);
$thumbnail = wp_get_attachment_image_src($thumbnail_id, $thumbnail_type);
$alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
$oAuthor = new Authors();
$authors = $oAuthor->getAuthorInfo($id);

if ($config === 'blog') {
    $thumbnail_url = $authors[0]['thumbnail_url'];
} else {
    $thumbnail_url = $thumbnail[0];
}
?>
<article class="c-article-item js-article-item">
  <img src="<?php echo $thumbnail_url;?>" alt="<?php echo $alt_text;?>">
  <h1><a href="<?php echo get_the_permalink($id);?>"><?php echo get_the_title($id);?></a></h1>
  <?php
  // If the 'show_excerpt' value has been passed to $config: Display the excerpt.
  if ($config === 'show_excerpt') { ?>
  <div class="meta">
    <span class="c-article-item__dateline"><?php echo get_the_time('j M Y', $id);?> by <?php echo $authors[0]['name'];?></span>
  </div>
    <div class="c-article-exceprt">
      <p><?php the_excerpt();?></p>
    </div>
  <?php } ?>
  <?php
  // If the 'blog' value has been passed to $config: Display the byline.
  if ($config === 'blog') {
  ?>
        <span class="c-article-item__byline"><?php echo $authors[0]['name'];?></span>
  <?php } ?>
</article>
