<?php
use MOJ\Intranet\Agency;

function searchfilter($query) {
  $oAgency = new Agency();
  $activeAgency = $oAgency->getCurrentAgency();
  $agency_name = $activeAgency['wp_tag_id'];

  if ( !is_admin() && $query->is_main_query() ) {
    if ($query->is_search) {
      $tax_query = array(
        array(
            'taxonomy' => 'agency',
            'field' => 'term_id',
            'terms' => $agency_name,
        ),
      );
      $query->set( 'tax_query', $tax_query );
    }
  }

  return $query;
}

add_filter('pre_get_posts','searchfilter');
