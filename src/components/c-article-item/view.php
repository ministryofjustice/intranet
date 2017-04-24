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
<article class="c-article-item">
  <img src="<?php echo $thumbnail[0];?>" alt="<?php echo $alt_text;?>">
  <h1><a href="<?php echo get_the_permalink($id);?>"><?php echo get_the_title($id);?></a></h1>
  <?php
  // If the 'featured_news' value has been passed to $config: Display the excerpt.
  if ($config === 'featured_news') { ?>
    <div class="c-article-item__excerpt">
      <p><?php echo $post->post_excerpt;?></p>
    </div>
  <?php } ?>
  <span class="c-article-item__dateline"><?php echo get_the_time('j M Y', $id);?></span>

  <?php
  // If the 'blog' value has been passed to $config: Display the byline.
  if ($config === 'blog') { ?>
    <span class="c-article-item__byline"><?php echo $authors;?></span>
  <?php } ?>
</article>
