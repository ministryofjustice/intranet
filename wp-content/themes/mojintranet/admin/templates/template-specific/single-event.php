<?php
// Webchat metabox
function event_details_callback($post) {
  $ns = 'event';
  wp_nonce_field( $ns.'_meta_box', $ns.'_meta_box_nonce' );
  $post_meta = get_post_meta($post->ID);
  ?>
  <table>
    <tr>
      <td>Location:</td>
      <td><input id='<?=$ns?>-location' name='<?=$ns?>-location' value='<?=$post_meta['_'.$ns.'-location'][0]?>'></td>
      <td width='20'></td>
      <td>
        <input type='checkbox' id='<?=$ns?>-allday' name='<?=$ns?>-allday' value='allday' <?=$post_meta['_'.$ns.'-allday'][0]?'checked':''?>>
        <label for='<?=$ns?>-allday'>All day event?</label>
      </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
      <td>Start Date:</td>
      <td><input id='<?=$ns?>-start-date' name='<?=$ns?>-start-date' value='<?=$post_meta['_'.$ns.'-start-date'][0]?>' class='datepicker'></td>
      <td width='20'></td>
      <td class='event-times'>Start Time:</td>
      <td class='event-times'><input type='time' id='<?=$ns?>-start-time' name='<?=$ns?>-start-time' value='<?=$post_meta['_'.$ns.'-start-time'][0]?>' class='timepicker'></td>
    </tr>
    <tr>
      <td>End Date:</td>
      <td><input id='<?=$ns?>-end-date' name='<?=$ns?>-end-date' value='<?=$post_meta['_'.$ns.'-end-date'][0]?>' class='datepicker'></td>
      <td width='20'></td>
      <td class='event-times'>End Time:</td>
      <td class='event-times'><input type='time' id='<?=$ns?>-end-time' name='<?=$ns?>-end-time' value='<?=$post_meta['_'.$ns.'-end-time'][0]?>' class='timepicker'></td>

    </tr>
  </table>
  <?php
}

function event_details_save($post_id) {
  $ns = 'event';
  if ( ! isset( $_POST[$ns.'_meta_box_nonce'] ) ) {
      return;
  }
  if ( ! wp_verify_nonce( $_POST[$ns.'_meta_box_nonce'], $ns.'_meta_box' ) ) {
      return;
  }
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return;
  }
  if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
      if ( ! current_user_can( 'edit_page', $post_id ) ) {
          return;
      }
  } else {
      if ( ! current_user_can( 'edit_post', $post_id ) ) {
          return;
      }
  }
  $field_array=array('location','start-date','start-time','end-date','end-time','allday');

  // Validation
  $data_ok = true;
  $mojintranet_errors = get_option( 'mojintranet_errors');
  if(in_array(get_post_status( $post_id ),array('publish','future'))) { // Only validate on publish
    // If no end date...
    if(!$_POST[$ns."-end-date"]) {
      $mojintranet_errors[]= "Please enter an end date to publish this event";
      $data_ok = false;
    }
    // If event finishes after start
    if(strtotime($_POST[$ns."-start-date"] . $_POST[$ns."-start-time"])>strtotime($_POST[$ns."-end-date"] . $_POST[$ns."-end-time"])) {
      $mojintranet_errors[]= "The event cannot end later than it starts! Please correct to publish this event";
      $data_ok = false;
    }
  }

  foreach ($field_array as $field) {
    if (isset($_POST[$ns . "-" . $field])) {
        $data = sanitize_text_field( $_POST[$ns . "-" . $field] );
        update_post_meta( $post_id, "_" . $ns . "-" . $field, $data );
    } else {
        delete_post_meta( $post_id, "_" . $ns . "-" . $field);
    }
  }
  if(!$data_ok) {
    // save error messages
    update_option('mojintranet_errors',$mojintranet_errors);

    // unhook this function to prevent indefinite loop
    remove_action('save_post', 'event_details_save',5);

    // update the post to change post status
    wp_update_post(array('ID' => $post_id, 'post_status' => 'draft'));

    // re-hook this function again
    add_action('save_post', 'event_details_save',5);
  }
}

?>
