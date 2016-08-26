<?php
//Left Hand Nav Metabox
function left_hand_nav_callback($post) {
	$lhs_menu_on = get_post_meta($post->ID, 'lhs_menu_on', true) != "0" ? true : false;

	if ($lhs_menu_on) {
		$lhs_menu_checked = "checked=\"checked\"";
	} else {
		$lhs_menu_checked = "";
	}

	?>

    <table class="form-table">
        <tr>
            <td>
                <input type="checkbox" name="lhs_menu_control" id="lhs_menu_control" <?=$lhs_menu_checked?>>
                <label for="lhs_menu_control">Show left-hand menu</label>
            </td>
        </tr>
    </table>

    <?php
}

function left_hand_nav_save($post_id) {
	$lhs_menu_on = $_POST['lhs_menu_control'] == 'on' ? 1 : 0;
	update_post_meta($post_id, 'lhs_menu_on', $lhs_menu_on);
}

// Hide preview button (temp solution until preview works properly)
global $pagenow;
if ('post.php' == $pagenow || 'post-new.php' == $pagenow) {
	add_action('admin_head', 'dw_custom_publish_box');
	function dw_custom_publish_box() {
		if (!is_admin()) {
			return;
		}

		$style = '';
		$style .= '<style type="text/css">';
		$style .= '#preview-action';
		$style .= '{display: none; }';
		$style .= '</style>';

		echo $style;
	}
}

function guidance_landing_options_callback($post) {

	$context = Agency_Context::get_agency_context();
	$field_name = 'dw_'. $context .'_guidance_bottom';

	$guidance_bottom = get_post_meta($post->ID, $field_name, true) == 1 ? true : false;

	if ($guidance_bottom) {
		$guidance_bottom_checked = "checked=\"checked\"";
	} else {
		$guidance_bottom_checked = "";
	}
	?>

	<table class="form-table">
		<tr>
			<td>
				<input type="checkbox" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" <?=$guidance_bottom_checked?>>
				<label for="<?php echo $field_name; ?>">Show on Guidance Bottom Section</label>
			</td>
		</tr>
	</table>

	<?php
}

function guidance_landing_options_save($post_id) {

	$context = Agency_Context::get_agency_context();
	$field_name = 'dw_'. $context .'_guidance_bottom';

	$guidance_bottom = $_POST[$field_name] == 'on' ? 1 : 0;
	update_post_meta($post_id, $field_name, $guidance_bottom);
}

// Force preview to work with custom meta - to be continued (TODO)!
/*
add_filter('_wp_post_revision_fields', 'dw_add_field_debug_preview');
function dw_add_field_debug_preview($fields){
$ns = 'content_tabs'; // Quick namespace variable
$tab_count = $_POST['tab-count'];
$fields["_".$ns."tab-count"] = $tab_count;
// for($tab=1;$tab<=$tab_count;$tab++) {
//     $section_count = $_POST["tab-".$tab."-section-count"];
//     for ($section=1;$section<=$section_count;$section++) {
//         $fields["debug_preview"] = "debug_preview";
//     }
// }
return $fields;
}
 */
