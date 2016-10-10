<?php
/**
 * Formats the event date stored in the DB
 * Filter: acf/update_value
 *
 * @param string $value - the value of custom field (date)
 */
function dw_format_event_dates($value){
  if(strlen($value) == 8) {
    $value = substr($value , 0, 4) . '-'. substr($value , 4, 2) . '-'. substr($value , 6, 2);
  }
  return $value;
}
add_filter('acf/update_value/key=field_57e8ead6a0890', 'dw_format_event_dates');
add_filter('acf/update_value/key=field_57e8ec4136787', 'dw_format_event_dates');
