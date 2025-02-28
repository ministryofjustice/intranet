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

?>

<!-- c-most-popular starts here -->
<section class="c-most-popular">
    <?php
    if (get_field($field_prefix . 'most_popular_text_1', 'option')) {
        echo '<h1 class="o-title o-title--subtitle">' . get_field($field_prefix . 'most_popular_title', 'option') . '</h1>';
    }
    ?>
  <ul>
    <?php
    for ($i = 0; $i <= 5; $i++) {
        $quickLinks[] = [
            'title' => get_field($field_prefix . 'most_popular_text_' . $i, 'option'),
            'url'   => get_field($field_prefix . 'most_popular_link_' . $i, 'option'),
        ];
        if (! empty($quickLinks[ $i ]['title'])) {
            echo '<li>
          <a class="c-most-popular--link" href="' . $quickLinks[ $i ]['url'] . '">' . $quickLinks[ $i ]['title'] . '</a>
        </li>';
        }
    }
    ?>
  </ul>
</section>
<!-- c-most-popular ends here -->
