<?php
// Webchat metabox
function coveritlive_id_callback($post) {
  $ns = 'webchat';
  wp_nonce_field( $ns.'_meta_box', $ns.'_meta_box_nonce' );
  $coveritlive_id = get_post_meta($post->ID, "_" . $ns . "-coveritlive-id",true);
  ?>
  <input id='<?=$ns?>-coveritlive-id' name='<?=$ns?>-coveritlive-id' value='<?=$coveritlive_id?>'>
  <?php
}

function coveritlive_id_save($post_id) {
  $ns = 'webchat';
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
  if (isset($_POST[$ns . "-coveritlive-id"])) {
      $data = sanitize_text_field( $_POST[$ns . "-coveritlive-id"] );
      update_post_meta( $post_id, "_" . $ns . "-coveritlive-id", $data );
  } else {
      delete_post_meta( $post_id, "_" . $ns . "-coveritlive-id");
  }
}
?>
