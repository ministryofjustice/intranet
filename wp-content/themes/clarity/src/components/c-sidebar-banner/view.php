<?php
use MOJ\Intranet\Agency;
$agency = get_intranet_code();

$enable_banner_right_side         = get_field( $agency . '_enable_banner_right_side', 'option' );
$banner_header                    = get_field( $agency . '_banner_sidebar_header', 'option' );
$homepage_sidebar_banner_image    = get_field( $agency . '_homepage_sidebar_banner_image', 'option' );
$homepage_sidebar_banner_link     = get_field( $agency . '_homepage_sidebar_banner_link', 'option' );
$homepage_sidebar_banner_alt_text = get_field( $agency . '_homepage_sidebar_banner_alt_text', 'option' );
?>

<?php if ( $enable_banner_right_side == true ) : ?>

<h1 class="o-title o-title--section"><?php echo $banner_header; ?></h1>
<!-- c-sidebar-banner starts here -->
<section class="c-sidebar-banner">
  <a href="<?php echo $homepage_sidebar_banner_link; ?>" class="sidebar-banner">
	  <img src="<?php echo $homepage_sidebar_banner_image; ?>" alt="<?php echo $homepage_sidebar_banner_alt_text; ?>">
  </a>
</section>
<!-- c-sidebar-banner ends here -->
<?php else : ?>
  <!-- No banner image selected, so nothing loaded. -->
<?php endif; ?>
