<?php
use MOJ\Intranet\Authors;

if ($config === 'archive'){
  $id = get_the_ID();
}else{
  $id = $data['id'];
}

$post_object = get_post($id);
$thumbnail_type = 'intranet-large';
$thumbnail_id = get_post_thumbnail_id($id);
$thumbnail = wp_get_attachment_image_src($thumbnail_id, $thumbnail_type);
$alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
$oAuthor = new Authors();
$authors = $oAuthor->getAuthorInfo($id);
$thumbnail_url = $thumbnail[0];

?>
<article class="c-article-item js-article-item">
  <?php 
    if (isset($thumbnail_url)){
      ?>
        <a href="<?php echo get_the_permalink($id);?>">
          <img src="<?php echo $thumbnail_url;?>" alt="<?php echo $alt_text;?>">
        </a>
      <?php 
    } else {
      ?>
        <a href="<?php echo get_the_permalink($id);?>">
          <img src="<?php echo $authors[0]['thumbnail_url'];;?>" alt="<?php echo $alt_text;?>">
        </a>
      <?php
    }
  ?>  
  <h1><a href="<?php echo get_the_permalink($id);?>"><?php echo get_the_title($id);?></a></h1>
  <?php 
    if ($config === 'show_date') {?>
      <div class="meta">
        <span class="c-article-item__dateline"><?php echo get_the_time('j M Y', $id);?></span>
      </div>
  <?php } ?>

  <?php 
    if ($config === 'show_date_and_excerpt') {?>
      <div class="c-article-exceprt">
        <p><?php echo get_the_excerpt($id);?></p>
      </div>
      <div class="meta">
        <span class="c-article-item__dateline"><?php echo get_the_time('j M Y', $id);?></span>
      </div>
  <?php } ?>

  <?php
  // If the 'show_excerpt' value has been passed to $config: Display the excerpt.
    if ($config === 'show_excerpt') { ?>
      <div class="c-article-exceprt">
        <p><?php echo get_the_excerpt($id);?></p>
      </div>
      <div class="meta">
        <span class="c-article-item__dateline"><?php echo get_the_time('j M Y', $id);?> by <?php echo $authors[0]['name'];?></span>
      </div>
  <?php } ?>
  <?php
  // If the 'blog' value has been passed to $config: Display the byline.
  if ($config === 'blog') {
  ?>
    <span class="c-article-item__dateline"><?php echo get_the_time('j M Y', $id);?></span>
  <?php } ?>
</article>
