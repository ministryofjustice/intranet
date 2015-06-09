<?php
if(!is_front_page()) {
  wp_redirect(site_url());
} else {
  locate_template( 'error.php', true );
}