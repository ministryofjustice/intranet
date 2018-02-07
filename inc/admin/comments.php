<?php

/**
 * Classes and their methods responsible for managing comments in WP admin
 *
 */
// Exit if accessed directly
if (! defined('ABSPATH')) {
    die();
}

/**
 * WP does not enable you to modify meta boxes, rather you need to remove
 * the default discussion meta box and replace it with a custom meta box with the new title or features you require.
 *
 */
add_action('add_meta_boxes_post', 'update_discussion_meta_box');
add_action('add_meta_boxes_news', 'update_discussion_meta_box');
add_action('add_meta_boxes_page', 'update_discussion_meta_box');

function update_discussion_meta_box()
{
    // Applies to all post types being used on the site
    global $post_type;

    remove_meta_box('commentstatusdiv', $post_type, 'normal');
    add_meta_box('commentstatusdiv', __('Discussion'), 'custom_discussion_meta_box', $post_type, 'normal', 'low');
}

function custom_discussion_meta_box()
{

  /**
   * Meta box render function
   *
   * @param  object $post Post object.
   * @since  1.0.0
   */
    $post_id = get_the_ID();
    $meta = get_post_meta($post_id);
    $discussion_meta_box_value = (isset($meta['discussion_meta_box_value'][0]) && '' !== $meta['discussion_meta_box_value'][0]) ? $meta['discussion_meta_box_value'][0] : '';

    wp_nonce_field('comment_discussion_meta_box', 'discussion_meta_box_nonce'); ?>
  	<style type="text/css">
  		.post_meta_extras p{margin: 20px;}
  		.post_meta_extras label{display:block; margin-bottom: 10px;}
  	</style>

  	<div class="post_meta_extras">
  		<p>
  			<label>
  				<input type="radio" name="discussion_meta_box_value" value="comments_on" <?php checked($discussion_meta_box_value, 'comments_on'); ?>>
  				<?php esc_attr_e('Comments on', 'clarity-theme'); ?>
  			</label>
  			<label>
  				<input type="radio" name="discussion_meta_box_value" value="comments_off" <?php checked($discussion_meta_box_value, 'comments_off'); ?>>
  				<?php esc_attr_e('Comments off', 'clarity-theme'); ?>
  			</label>
  			<label>
  				<input type="radio" name="discussion_meta_box_value" value="comments_closed" <?php checked($discussion_meta_box_value, 'comments_closed'); ?>>
  				<?php esc_attr_e('Comments closed', 'clarity-theme'); ?>
  			</label>
  		</p>
    </div>
  	<?php

}

/**
 * Save controls from the meta boxes
 *
 * @param  int $post_id Current post id.
 * @since 1.0.0
 */
 add_action('save_post', 'discussion_save_metaboxes');

function discussion_save_metaboxes($post_id)
{
    $post_id = get_the_ID();
    /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times. Add as many nonces, as you
     * have metaboxes.
     */
    if (!isset($_POST['discussion_meta_box_nonce']) || !wp_verify_nonce(sanitize_key($_POST['discussion_meta_box_nonce']), 'comment_discussion_meta_box')) { // Input var okay.
        return $post_id;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    /*
     * If this is an autosave, our form has not been submitted,
     * so we don't want to do anything.
     */
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    /* Ok to save */
    if (isset($_POST['discussion_meta_box_value'])) { // Input var okay.
        update_post_meta($post_id, 'discussion_meta_box_value', sanitize_text_field(wp_unslash($_POST['discussion_meta_box_value']))); // Input var okay.
    }
}
