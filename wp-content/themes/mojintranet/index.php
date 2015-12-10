<?php
if(!is_front_page()) {
  wp_redirect(site_url());
} else {
  locate_template( 'page_error.php', true );
}
