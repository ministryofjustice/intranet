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
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
      <td>Start Date:</td>
      <td><input id='<?=$ns?>-start-date' name='<?=$ns?>-start-date' value='<?=$post_meta['_'.$ns.'-start-date'][0]?>' class='datepicker'></td>
      <td width='20'></td>
      <td>Start Time:</td>
      <td><input id='<?=$ns?>-start-time' name='<?=$ns?>-start-time' value='<?=$post_meta['_'.$ns.'-start-time'][0]?>' class='timepicker'></td>
    </tr>
    <tr>
      <td>End Date:</td>
      <td><input id='<?=$ns?>-end-date' name='<?=$ns?>-end-date' value='<?=$post_meta['_'.$ns.'-end-date'][0]?>' class='datepicker'></td>
      <td width='20'></td>
      <td>End Time:</td>
      <td><input id='<?=$ns?>-end-time' name='<?=$ns?>-end-time' value='<?=$post_meta['_'.$ns.'-end-time'][0]?>' class='timepicker'></td>
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
  $field_array=array('location','start-date','start-time','end-date','end-time');
  foreach ($field_array as $field) {
    if (isset($_POST[$ns . "-" . $field])) {
        $data = sanitize_text_field( $_POST[$ns . "-" . $field] );
        update_post_meta( $post_id, "_" . $ns . "-" . $field, $data );
    } else {
        delete_post_meta( $post_id, "_" . $ns . "-" . $field);
    }
  }
}
?>
