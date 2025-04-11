<?php

/**
 * Clarity theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Clarity theme
 * @since 1.0
 */

if (defined('WP_CLI') && WP_CLI) {
    require_once 'inc/commands/DocumentRevisionReconcile.php';
    require_once 'inc/commands/FindDocumentRefs.php';
    require_once 'inc/commands/SyncUserRoles.php';
}

require_once 'inc/admin/acf-field-group.php';
require_once 'inc/admin/admin-commands.php';

require_once 'inc/admin/agency_taxonomies/utilities/agency-editor.php';
require_once 'inc/admin/agency_taxonomies/utilities/agency-context.php';
require_once 'inc/admin/agency_taxonomies/utilities/region-context.php';

// Load after above utilities
require_once 'inc/admin/agency_taxonomies/taxonomies.php';

require_once 'inc/admin/campaign-content-template.php';
require_once 'inc/admin/comments.php';
require_once 'inc/admin/dashboard.php';
require_once 'inc/admin/custom-page-attribute-box.php';
require_once 'inc/admin/hide-templates-from-editors.php';
require_once 'inc/admin/lefthand-menu.php';
require_once 'inc/admin/intranet-archive-link.php';
require_once 'inc/admin/list-tables.php';
require_once 'inc/admin/login-with-email.php';
require_once 'inc/admin/page.php';
require_once 'inc/admin/permission-display-page.php';
require_once 'inc/admin/plugins/co-authors-plus.php';
require_once 'inc/admin/plugins/polls.php';
require_once 'inc/admin/plugins/wordpress-simple-history.php';
require_once 'inc/admin/plugins/wp-elasticsearch.php';
require_once 'inc/admin/plugins/wp-document-revisions.php';
require_once 'inc/admin/plugins/wp-offload-media.php';
require_once 'inc/admin/plugins/wp-sentry.php';
require_once 'inc/admin/prior-party/prior-party-banner-admin.php';
require_once 'inc/admin/prior-party/prior-party-banner-email.php';
require_once 'inc/admin/prior-party/prior-party-banner.php';
require_once 'inc/admin/remove-customizer.php';
require_once 'inc/admin/suppress-wp-update-msg.php';
require_once 'inc/admin/tinymce-editor-settings.php';
require_once 'inc/admin/transient-admin-notices.php';
require_once 'inc/admin/users/add-acf-capabilities.php';
require_once 'inc/admin/users/add-notes-from-antonia.php';
require_once 'inc/admin/users/remove-agency-admin-admin-access.php';
require_once 'inc/admin/users/user-access-and-security.php';
require_once 'inc/admin/wp-admin-bar.php';

require_once 'inc/about-us.php';
require_once 'inc/acf.php';
require_once 'inc/maintenance.php';
require_once 'inc/amazon-s3-and-cloudfront-assets.php';
require_once 'inc/amazon-s3-and-cloudfront-for-minio.php';
require_once 'inc/amazon-s3-and-cloudfront-signing.php';
require_once 'inc/amazon-s3-and-cloudfront.php';

require_once 'inc/api/campaign-api.php';
require_once 'inc/api/get-campaign-posts-api.php';
require_once 'inc/api/get-news-rest-api.php';
require_once 'inc/api/get-notes-rest-api.php';
require_once 'inc/api/get-campaign-news-api.php';
require_once 'inc/api/get-category-news-api.php';
require_once 'inc/api/intranet-v1-api.php';
require_once 'inc/api/register-api-fields.php';


require_once 'inc/admin-branding.php';
require_once 'inc/autoloader.php';
require_once 'inc/elasticsearch-on-opensearch.php';
require_once 'inc/cookies.php';
require_once 'inc/comments.php';
require_once 'inc/constants.php';
require_once 'inc/content-filter/search-query.php';
require_once 'inc/content-filter/search.php';
require_once 'inc/enqueue.php';
require_once 'inc/form-builder.php';
require_once 'inc/forms.php';
require_once 'inc/get-component.php';
require_once 'inc/get-intranet-code.php';
require_once 'inc/guidance-and-forms.php';

require_once 'inc/helpers/debug.php';
require_once 'inc/helpers/taggr.php';
require_once 'inc/helpers/validation.php';

require_once 'inc/images.php';
require_once 'inc/languages.php';
require_once 'inc/mail.php';
require_once 'inc/menu.php';
require_once 'inc/utilities.php';
require_once 'inc/pagination.php';

require_once 'inc/post-types/post.php';
require_once 'inc/post-types/event.php';
require_once 'inc/post-types/news.php';
require_once 'inc/post-types/regional-news.php';
require_once 'inc/post-types/regional-page.php';
require_once 'inc/post-types/team-area.php';
require_once 'inc/post-types/webchat.php';
require_once 'inc/post-types/notes-from-antonia.php';

require_once 'inc/preselect.php';
require_once 'inc/rewrite_rules.php';
require_once 'inc/relevanssi.php';
require_once 'inc/shortcodes.php';
require_once 'inc/security.php';
require_once 'inc/table-modification.php';
require_once 'inc/updates.php';
require_once 'inc/uploads.php';
require_once 'inc/whitelisted-emails.php';


new MOJ\Intranet\AdminBranding();
new MOJ\Intranet\WPDocumentRevisions();
new MOJ\Intranet\WPOffloadMedia();
new MOJ\Intranet\WPElasticPress();

$search = new MOJ\Intranet\Search();
$search->hooks();

add_filter('simple_history/core_dropins', 'filter_dropins', 10, 1 );

function filter_dropins($dropins)
{
    // error_log(print_r($dropins, true));

    $remove = [
        // 'Simple_History\Dropins\Donate_Dropin',
        'Simple_History\Dropins\Sidebar_Stats_Dropin',
        'Simple_History\Dropins\Sidebar_Add_Ons_Dropin',
    ];

    // Remove the dropins we don't want
    

    $dropins = array_filter($dropins, function ($v, $k) use ($remove) {
        return !in_array($v, $remove);
    }, ARRAY_FILTER_USE_BOTH);


    return $dropins;
}

add_filter ('simple_history/SidebarDropin/default_sidebar_boxes', 'filter_sideboxes', 10, 1);

function filter_sideboxes($sidebar_boxes)
{
    unset($sidebar_boxes['boxReview']);

    // If not admin, remove the sidebar boxes
    if (!current_user_can('administrator')) {
        unset($sidebar_boxes['boxSupport']);
        unset($sidebar_boxes['boxDonate']);
    }

    return $sidebar_boxes;
}

// add_filter('simple_history/post_logger/context', 'handle_acf_context', 10, 5);

function handle_acf_context($context, $_old_data, $_new_data, $old_meta, $new_meta)
{
    error_log('in handle acf context');
    error_log(print_r($old_meta['prior_party_banner'], true));
    error_log(print_r($new_meta['prior_party_banner'], true));

    $arr_meta_keys_to_ignore = array(
        // Ignore our custom event tracking fields.
        // We don't need to track the tracking :)
        '_prior_party_banner_event_timestamp',
        '_prior_party_banner_event_details'
    );

    foreach ( $arr_meta_keys_to_ignore as $key_to_ignore ) {
        unset( $old_meta[ $key_to_ignore ] );
        unset( $new_meta[ $key_to_ignore ] );
    }


    // Look for added custom fields/meta.
    foreach ($new_meta as $meta_key => $meta_value) {
        if (! isset($old_meta[$meta_key])) {
            error_log('meta key added: ' . $meta_key);
            $context["post_prev_meta_$meta_key"] = null;
            $context["post_new_meta_$meta_key"] = $meta_value;
        }
    }

    // Look for changed custom fields/meta.
    foreach ($old_meta as $meta_key => $meta_value) {
        if (isset($new_meta[$meta_key]) && json_encode($old_meta[$meta_key]) !== json_encode($new_meta[$meta_key])) {
            error_log('meta key changed: ' . $meta_key);
            $context["post_prev_meta_$meta_key"] = $meta_value;
            $context["post_new_meta_$meta_key"] = $new_meta[$meta_key];
        }
    }

    return $context;
}

// require_once 'inc/admin/plugins/developer-loggers-for-simple-history/developer_loggers.php';


// add_filter('simple_history/post_logger/post_updated/diff_table_output', 'my_function', 10, 2);

// function my_function(
//     $diff_table_output,
//     $context
// ) {
//     // error_log('diff table output: ' . print_r($diff_table_output, true));
//     // error_log('context: ' . print_r($context, true));
//     return $diff_table_output;
// }

// apply_filters(
// 	'simple_history_log',
// 	'This is a logged message'
// );

// function my_acf_update_value($value, $post_id, $field)
// {

//     // if($field['value'] === null && )

//     $existing_value = get_field($field['key'], $post_id);

//     error_log('existing value: ' . print_r($existing_value, true));

//     if($existing_value === $value) {
//         error_log('returning matching values');
//         return $value;
//     }

//     if (is_string($value) && empty($value) && empty($field['value'])) {
//         error_log('returning empty string');
//         return $value;
//     }

//     // error_log(print_r($field['value'], true));

    
//     // error_log(print_r($field, true));
//     // error_log(print_r(gettype($field['value']), true));
//     // error_log('is string: ' . (is_string($value) ? 'true' : 'false'));
//     // error_log('is value empty: ' . (empty($value) ? 'true' : 'false'));
//     // error_log('is field empty: ' . (empty($field['value']) ? 'true' : 'false'));

//     apply_filters(
//         'simple_history_log',
//         'User {username} updated field {field_label} from {field_value} to {new_value} for post with ID {post_id}.',
//         [
//             'username' => 'admin',
//             'post_id' => $post_id,
//             'field_value' => $field['value'],
//             'field_label' => $field['label'],
//             'new_value' => $value,
//             '_occasionsID' => "postID:{$post_id},fieldID:{$field['key']},action:fieldUpdated"
//         ],
//         'info'
//     );

//     // don't forget to return to be saved in the database
//     return $value;
// }

// // acf/update_value - filter for every field
// add_filter('acf/update_value', 'my_acf_update_value', 90, 3);
