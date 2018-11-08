<?php
if(!is_front_page()) {
  wp_redirect(home_url());
} else {
  locate_template( 'page_error.php', true );
}
