<?php

use MOJ\Intranet\Multisite;

$blog_is_single_agency = Multisite::isSingleAgencyBlog();
// If we are on a multisite blog and it only has one agency, then the field prefix is empty.
$field_prefix = '';

if(!$blog_is_single_agency) {
  // Here, we can't get the agency from the multisite, so we are still on blog id 1.
  // Get the agency from the cookie.
  $agency = get_intranet_code();
  // Set the field prefix to the agency shortcode.
  $field_prefix = $agency . '_';
}

$enable_banner_right_side         = get_field($field_prefix . 'enable_banner_right_side', 'option');
$banner_header                    = get_field($field_prefix . 'banner_sidebar_header', 'option');
$homepage_sidebar_banner_image    = get_field($field_prefix . 'homepage_sidebar_banner_image', 'option');
$homepage_sidebar_banner_link     = get_field($field_prefix . 'homepage_sidebar_banner_link', 'option');
$homepage_sidebar_banner_alt_text = get_field($field_prefix . 'homepage_sidebar_banner_alt_text', 'option');
?>

<?php if ($enable_banner_right_side) : ?>
<!-- c-aside-banner starts here -->
<section class="c-aside-banner">
  <h1 class="o-title o-title--subtitle"><?= $banner_header ?></h1>
  
  <a href="<?= $homepage_sidebar_banner_link ?>" class="c-aside-banner--link">

      <img src="<?= $homepage_sidebar_banner_image ?>" alt="<?= $homepage_sidebar_banner_alt_text ?>">
  </a>
</section>
<!-- c-aside-banner ends here -->

<?php else : ?>
  <!-- No banner image selected, so no banner loaded. -->
<?php endif; ?>
