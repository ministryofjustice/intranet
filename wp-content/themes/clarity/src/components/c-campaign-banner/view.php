<?php
$banner_id    = get_field( 'dw_page_banner' );
$banner_url   = get_field( 'dw_banner_url' );
$banner_image = wp_get_attachment_image_src( $banner_id, 'full' );
?>
<?php if ( ! empty( $banner_image ) ) : ?>
<!-- c-campaign-banner starts here -->
<section class="c-campaign-banner">
	<?php if ( ! empty( $banner_url ) ) : ?>
	<a href="<?php echo $banner_url; ?>">
	<?php endif ?>
	<img src="<?php echo $banner_image[0]; ?>" class="campaign-banner" />
	<?php if ( ! empty( $banner_url ) ) : ?>
	</a>
	<?php endif ?>
</section>
<!-- c-campaign-banner ends here -->
<?php endif ?>
