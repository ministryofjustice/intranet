<!-- c-full-width-banner starts here -->
<section class="c-full-width-banner">
  <a href="<?php the_field('dw_banner_url')?>">
    <?php $image = wp_get_attachment_image_src(get_field('dw_page_banner'), 'full'); ?>
    <img src="<?php echo $image[0]; ?>" alt="" />
  </a>
</section>
<!-- c-full-width-banner ends here -->