<?php

use MOJ\Intranet\Multisite;

$blog_is_single_agency = Multisite::isSingleAgencyBlog();
// If we are on a multisite blog and it only has one agency, then the field prefix is empty.
$field_prefix = $blog_is_single_agency ? '' : get_intranet_code() . '_'; 
// $agency = get_intranet_code();
?>

<!-- c-external-services starts here -->
<section class="c-external-services">
  <ul>
    <?php

    for ($i = 0; $i <= 10; $i++) {
        if (have_rows($field_prefix . 'external_services', 'option')) :
            while (have_rows($field_prefix . 'external_services', 'option')) :
                the_row();

                $title = esc_attr(get_sub_field('external_services_title_' . $i, 'option'));
                $url   = esc_url(get_sub_field('external_services_url_' . $i, 'option'));
                if ($url != '' && $title !='') {
                    echo '<li><a href="' . $url . '" class="app-list-link">' . $title . '</a></li>';
                }
            endwhile;
        endif;
    }

    ?>
  </ul>
</section>
<!-- c-external-services ends here -->
