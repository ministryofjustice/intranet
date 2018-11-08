<?php
  use MOJ\Intranet\Agency;
  $agency = get_intranet_code();

  $switch       = get_field( $agency . '_homepage_video_switch', 'option' );
  $youtube_url  = get_field( $agency . '_homepage_video_youtubeurl', 'option' );
  $title        = get_field( $agency . '_homepage_video_title', 'option' );
  $excerpt      = get_field( $agency . '_homepage_video_excerpt', 'option' );

  global $wp_embed;
?>
<?php if($switch === true) : ?>
<!-- c-homepage-video starts here -->
<section class="c-homepage-video">
  <h1 class="o-title o-title--section"><?php echo $title; ?></h1>
  <?php echo $wp_embed->run_shortcode('[embed ]'.$youtube_url.'[/embed]'); ?>
  <p><?php echo $excerpt; ?></p>
</section>
<!-- c-homepage-video ends here -->
<?php else: endif; ?>
