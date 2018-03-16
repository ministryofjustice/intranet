<?php
/*
Plugin Name: DW Live Drafts
Description: Adds ability to create a draft of a live page or post.
Version: 0.1

Based on Live Drafts plugin (v3.0.3)
*/
/*  Copyright 2011  Stephen Sandison  (email : stephen.sandison@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Note that original namespacing has been retained to eliminate the chance of
// a clash between this plugin and the original Live Drafts plugin

if (!class_exists('liveDrafts')) {

	class liveDrafts {

		/* PHP4 Contstructor */
		function liveDrafts () {

			// Admin head
			add_action('admin_head-post.php', array($this, 'adminHead'));

			// Pre-post update
			add_action('pre_post_update', array($this, 'prePostUpdate'));

      // Save post action
			add_action('save_post', array($this, 'postUpdate'), 10);
			add_action('publish_future_post', array($this, 'postUpdate'), 10);

		}

		function adminHead () {
			global $post;

			// Only show on published pages
			if (in_array($post->post_type, array('post', 'page')) && $post->post_status == 'publish') {
				?>
				<script type="text/javascript" >

					// Add save draft button to live pages
					jQuery(document).ready(function() {

						jQuery('<input type="submit" class="button button-highlighted" tabindex="4" value="Save Draft" id="save-post" name="save">').prependTo('#save-action');

					});

				</script>
				<?php
			}

		}

		function prePostUpdate ($id) {

			// Check if this is an auto save routine. If it is we dont want to do anything
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
				return $id;

			// Only continue if this request is for the post or page post type
			if (!in_array($_POST['post_type'], array('post', 'page'))) {
				return $id;
			}

			// Check permissions
			if (!current_user_can('edit_' . ($_POST['post_type'] == 'posts' ? 'posts' : 'page'), $id )) {
		  		return $id;
		  	}

			// Catch only when a draft is saved of a live page
			if ($_REQUEST['save'] == 'Save Draft' && $_REQUEST['post_status'] == 'publish') {

				// Duplicate post and set as a draft
				$draftPost = array(
				  'menu_order' => $_REQUEST['menu_order'],
				  'comment_status' => ($_REQUEST['comment_status'] == 'open' ? 'open' : 'closed'),
				  'ping_status' => ($_REQUEST['ping_status'] == 'open' ? 'open' : 'closed'),
				  'post_author' => $_REQUEST['post_author'],
				  'post_category' => (isset($_REQUEST['post_category']) ? $_REQUEST['post_category'] : array()),
				  'post_content' => $_REQUEST['content'],
				  'post_excerpt' => $_REQUEST['excerpt'],
				  'post_parent' => $_REQUEST['parent_id'],
				  'post_password' => $_REQUEST['post_password'],
				  'post_status' => 'draft',
				  'post_title' => $_REQUEST['post_title'],
				  'post_type' => $_REQUEST['post_type'],
				  'tags_input' => (isset($_REQUEST['tax_input']['post_tag']) ? $_REQUEST['tax_input']['post_tag'] : '')
				);

				// Insert the post into the database
				$newId = wp_insert_post($draftPost);

				// Custom meta data
				$custom = get_post_custom($id);
				foreach ($custom as $ckey => $cvalue) {
					if ($ckey != '_edit_lock' && $ckey != '_edit_last') {
						foreach ($cvalue as $mvalue) {
							add_post_meta($newId, $ckey, $mvalue, true);
						}
					}
				}

				// Add a hidden meta data value to indicate that this is a draft of a live page
				update_post_meta($newId, '_pc_liveId', 	$id);


				// Send user to new edit page
				wp_redirect(admin_url('post.php?action=edit&post=' . $newId));
				exit();

			}

		}

		function postUpdate($id) {
			if ((isset($_REQUEST['publish']) && $_REQUEST['publish']!='Schedule') || (defined( 'DOING_CRON' ) && DOING_CRON)) {

				// Check for post meta that identifies this as a 'live draft'
				$_pc_liveId = get_post_meta($id, '_pc_liveId', true);

				// If post meta exists then replace live page
				if ($_pc_liveId != false) {

					// Duplicate post and set as a draft
					$updatedPost = array(
					  'ID' => $_pc_liveId,
					  'menu_order' => $_REQUEST['menu_order'],
					  'comment_status' => ($_REQUEST['comment_status'] == 'open' ? 'open' : 'closed'),
					  'ping_status' => ($_REQUEST['ping_status'] == 'open' ? 'open' : 'closed'),
					  'post_author' => $_REQUEST['post_author'],
					  'post_category' => (isset($_REQUEST['post_category']) ? $_REQUEST['post_category'] : array()),
					  'post_content' => $_REQUEST['content'],
					  'post_excerpt' => $_REQUEST['excerpt'],
					  'post_parent' => $_REQUEST['parent_id'],
					  'post_password' => $_REQUEST['post_password'],
					  'post_status' => 'publish',
					  'post_title' => $_REQUEST['post_title'],
					  'post_type' => $_REQUEST['post_type'],
					  'tags_input' => (isset($_REQUEST['tax_input']['post_tag']) ? $_REQUEST['tax_input']['post_tag'] : '')
					);

					// Insert the post into the database
					wp_update_post($updatedPost);

					// Clear existing meta data
					$existing = get_post_custom($_pc_liveId);
					foreach ($existing as $ekey => $evalue) {
						delete_post_meta($_pc_liveId, $ekey);
					}

					// New custom meta data - from draft
					$custom = get_post_custom($id);
					foreach ($custom as $ckey => $cvalue) {
						if ($ckey != '_edit_lock' && $ckey != '_edit_last' && $ckey != '_pc_liveId') {
							foreach ($cvalue as $mvalue) {
								add_post_meta($_pc_liveId, $ckey, $mvalue, true);
							}
						}
					}

					// Delete draft post, force delete since 2.9, no sending to trash
					wp_delete_post($id, true);

					// Send user to live edit page
					wp_redirect(admin_url('post.php?action=edit&post=' . $_pc_liveId));
					exit();

				}

			}
		}


	}

	// Create an object from the class when the admin_init action fires
	add_action ('init', create_function('', 'global $liveDrafts; $liveDrafts = new liveDrafts();'));

}

?>
