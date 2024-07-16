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
require_once 'inc/admin/list-tables.php';
require_once 'inc/admin/login-with-email.php';
require_once 'inc/admin/page.php';
require_once 'inc/admin/permission-display-page.php';
require_once 'inc/admin/plugins/co-authors-plus.php';
require_once 'inc/admin/plugins/polls.php';
require_once 'inc/admin/remove-customizer.php';
require_once 'inc/admin/suppress-wp-update-msg.php';
require_once 'inc/admin/tinymce-editor-settings.php';
require_once 'inc/admin/users/add-acf-capabilities.php';
require_once 'inc/admin/users/add-notes-from-antonia.php';
require_once 'inc/admin/users/add-agency-admin.php';
require_once 'inc/admin/users/add-agency-editor.php';
require_once 'inc/admin/users/add-regional-editor.php';
require_once 'inc/admin/users/add-team-lead.php';
require_once 'inc/admin/users/add-team-author.php';
require_once 'inc/admin/users/delete-roles.php';
require_once 'inc/admin/users/user-access-and-security.php';
require_once 'inc/admin/wp-admin-bar.php';

require_once 'inc/about-us.php';
require_once 'inc/acf.php';
require_once 'inc/amazon-s3-and-cloudfront-for-minio.php';
require_once 'inc/amazon-s3-and-cloudfront-signing.php';
require_once 'inc/amazon-s3-and-cloudfront.php';

require_once 'inc/api/get-posts-rest-api.php';
require_once 'inc/api/campaign-api.php';
require_once 'inc/api/get-campaign-posts-api.php';
require_once 'inc/api/get-news-rest-api.php';
require_once 'inc/api/get-notes-rest-api.php';
require_once 'inc/api/get-campaign-news-api.php';
require_once 'inc/api/get-category-news-api.php';
require_once 'inc/api/intranet-v1-api.php';
require_once 'inc/api/register-api-fields.php';


require_once 'inc/autoloader.php';
require_once 'inc/elasticsearch-on-opensearch.php';
require_once 'inc/cookies.php';
require_once 'inc/comments.php';
require_once 'inc/constants.php';
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
require_once 'inc/pagination-newscategory.php';

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
require_once 'inc/uploads.php';
require_once 'inc/whitelisted-emails.php';

/** Environment Notice **/
require_once 'inc/environment-notice.php';


add_action('save_post', function ($post_id, $post) {
    if ($post->post_name === 'agency-switcher') {
        update_post_meta($post_id, '_wp_page_template', 'agency-switcher.php');
    }
}, 99, 2);


