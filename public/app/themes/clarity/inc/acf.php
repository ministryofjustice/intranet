<?php

require_once ABSPATH . 'wp-admin/includes/screen.php';

/**
 * Pick up ACF fields from parent theme
 */
add_filter('acf/settings/load_json', 'my_acf_json_load_point');

function my_acf_json_load_point($paths)
{
    // append path
    $paths[] = get_template_directory() . '/acf-json';

    // return
    return $paths;
}

/***
 *
 * Create homepage section in lefthand menu - ACF options
 * https://www.advancedcustomfields.com/resources/acf_add_options_page/
 */
add_action('init', 'homesettings_option_pages');

function homesettings_option_pages()
{
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(
            array(
                'page_title' => 'Homepage',
                'capability' => 'homepage_all_access',
                'icon_url' => 'dashicons-admin-home',
                'menu_title' => 'Homepage',
                'menu_slug' => 'homepage-settings',
                'position' => '2',
                'redirect' => false,
            )
        );
    }
}

add_filter('acf/load_field', 'home_option_fields');

/**
 * Modify the field name to include the agency context
 *
 * The is legacy behaviour, pre migration to multisite.
 * Options for agencies are stored in the database with option names like options_laa_beta_banner_text
 * If we are on a migrated multisite blog, then the field is returned unmodified e.g. options_beta_banner_text
 * 
 * @see /wp/wp-admin/admin.php?page=homepage-settings
 *
 * @param mixed $field - the field array
 * @return mixed $field - the modified field array
 */
function home_option_fields($field)
{
    $context = Agency_Context::get_agency_context();

    // If we are on a migrated multisite blog, then the field is returned unmodified
    if (false === $context) {
        return $field;
    }

    // Continue with logic to work out if the field name should be modified...

    // List of fields that are agency wide - these will never be modified
    $agency_wide_fields = ['enable_agency_wide_banner', 'agency_wide_banner_title', 'agency_wide_banner_message', 'agency_wide_banner_type', 'agency_wide_banner_link'];

    // If the field is an agency wide field, return it unmodified
    if (in_array($field['name'], $agency_wide_fields)) {
        return $field;
    }

    // If we are not on the homepage settings page, return the field unmodified
    if (get_current_screen()?->id !== 'toplevel_page_homepage-settings') {
        return $field;
    }

    // Modify the field name to include the agency context
    $field['name'] = $context . '_' . $field['name'];

    return $field;
}


/***
 *
 * Create header section in lefthand menu - ACF options
 * https://www.advancedcustomfields.com/resources/acf_add_options_page/
 */
add_action('init', 'headersettings_option_pages');

function headersettings_option_pages()
{
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(
            array(
                'page_title' => 'Header settings',
                'capability' => 'homepage_all_access',
                'icon_url' => 'dashicons-editor-table',
                'menu_title' => 'Header',
                'menu_slug' => 'header-settings',
                'position' => '1',
                'redirect' => false,
            )
        );
    }
}

add_filter('acf/load_field', 'header_option_fields');

/**
 * Modify the field name to include the agency context
 *
 * The is legacy behaviour, pre migration to multisite.
 * Options for agencies are stored in the database with option names like options_external_services_external_services_title_1
 * If we are on a migrated multisite blog, then the field is returned unmodified e.g. options_laa_external_services_external_services_title_1
 * 
 * @see r/wp/wp-admin/admin.php?page=header-settings
 *
 * @param mixed $field - the field array
 * @return mixed $field - the modified field array
 */
function header_option_fields($field)
{
    $context = Agency_Context::get_agency_context();

    // If we are on a migrated multisite blog, then the field is returned unmodified
    if (false === $context) {
        return $field;
    }

    if (get_current_screen()?->id === 'toplevel_page_header-settings') {
        $field['name'] = $context . '_' . $field['name'];
    }
    return $field;
}

// ‘Admin Only’ - ‘Yes/No’ setting to all fields allowing them to be hidden from non admin users - https://www.advancedcustomfields.com/resources/adding-custom-settings-fields/
function my_admin_only_render_field_settings($field)
{
    acf_render_field_setting(
        $field,
        array(
            'label' => __('Admin Only?'),
            'instructions' => '',
            'name' => 'admin_only',
            'type' => 'true_false',
            'ui' => 1,
        ),
        true
    );
}

add_action('acf/render_field_settings', 'my_admin_only_render_field_settings');

// This will allow us to check if the current user is an administrator, and if not, prevent the field from being displayed.
function my_admin_only_prepare_field($field)
{

    // bail early if no 'admin_only' setting
    if (empty($field['admin_only'])) {
        return $field;
    }
    // return false if is not admin (removes field)
    if (!current_user_can('administrator')) {
        return false;
    }

    return $field;
}

add_filter('acf/prepare_field', 'my_admin_only_prepare_field');


// hide drafts
add_filter('acf/fields/post_object/query', 'relationship_options_filter', 10, 3);
function relationship_options_filter($args, $field, $the_post)
{
    $args['post_status'] = ['publish'];
    $args['orderby'] = [
        'date' => 'DESC',
        'title' => 'ASC'
    ];

    return $args;
}

add_filter('acf/fields/post_object/result', 'my_acf_fields_post_object_result', 10, 4);
function my_acf_fields_post_object_result($text, $post, $field, $post_id)
{
    // always break
    $text .= '<br>';

    if ($post->post_type === 'page' && str_starts_with($text, '- ')) {
        $text = preg_replace('|- .*?(\w.*)|', '$1', $text);

        if ($parent_title = get_post_parent($post->post_parent)->post_title) {
            $text .= '<small>' . $parent_title . ' </small> &nbsp;&nbsp; |  &nbsp;&nbsp;';
        }
    }

    $text .= '<small>' . date('D d M Y H:i', strtotime($post->post_date)) . ' </small>';

    return $text;
}

add_filter('acf/location/rule_types', 'dw_acf_rule_type_agency_context');
function dw_acf_rule_type_agency_context($choices)
{
    // This location is only used by a single field group: homepage-settings
    // TODO It will need to be refactored after migrating to multisite.
    $choices['User']['agency_context'] = 'Current Agency Context';
    return $choices;
}

function dw_acf_rule_values_agency_context($choices)
{

    $agencies = get_terms('agency', array(
        'hide_empty' => false,
    ));

    foreach ($agencies as $agency) {
        $choices[$agency->slug] = $agency->name;
    }

    return $choices;
}

add_filter('acf/location/rule_values/agency_context', 'dw_acf_rule_values_agency_context');

function dw_acf_rule_match_agency_context($match, $rule, $options)
{

    $context = Agency_Context::get_agency_context();
    $match = false;

    if ('==' == $rule['operator']) {
        if ($context == $rule['value']) {
            $match = true;
        }
    } elseif ('!=' == $rule['operator']) {
        if ($context != $rule['value']) {
            $match = true;
        }
    }

    return $match;
}

add_filter('acf/location/rule_match/agency_context', 'dw_acf_rule_match_agency_context', 10, 3);

/**
 * This function hides the 'Escaped HTML notice' for non-admins.
 * 
 * @see https://www.advancedcustomfields.com/blog/acf-6-2-5-security-release/#detection-and-notice-information
 */

function acf_hide_notice_for_non_admins()
{
    // If we're on the frontend, return false to keep logging enabled.
    if (!is_admin()) {
        return false;
    }

    // We're on an admin screen.

    // Set the value false if user is admin. Set it to true for non-admins.
    return !current_user_can('administrator');
}

add_filter('acf/admin/prevent_escaped_html_notice', 'acf_hide_notice_for_non_admins');

/**
 * Allow iframe tags to be returned by ACF the_field & the_sub_field.
 * 
 * @param array $tags the allowed html tags.
 * @param string  $context the wp_kses context.
 * 
 * @return array $tags the filtered allowed html tags.
 */

function acf_add_allowed_iframe_tag($tags, $context)
{
    if ($context === 'acf') {
        $tags['iframe'] = array(
            'src'             => true,
            'height'          => true,
            'width'           => true,
            'frameborder'     => true,
            'allowfullscreen' => true,
        );
    }

    return $tags;
}

add_filter('wp_kses_allowed_html', 'acf_add_allowed_iframe_tag', 10, 2);


/**
 * An inline script so that the 'Publish' button is disabled on production.
 * 
 * This is a guard rail to stop developers from updating ACF field groups on production.
 * It's only client side, and is not intended as a security measure.
 * 
 * The proper way to update ACF field groups is to:
 * - modify the field group locally
 * - commit the generated .json files to the repository
 * - deploy to dev, staging & production
 * 
 * @see https://www.advancedcustomfields.com/resources/acf-field_group-admin_footer/
 */
function acfFilterFieldGroupEditsClientSide()
{
    if (getenv('WP_ENV') === 'development') {
        return;
    }
?>
    <script>
        jQuery(document).ready(function($) {
            $('.acf-btn.acf-publish').attr('disabled', 'disabled').append(' (disabled on production)');
        });
    </script>
<?php
}

add_action('acf/field_group/admin_footer', 'acfFilterFieldGroupEditsClientSide');


/**
 * Prevent ACF field groups from being saved on production.
 * 
 * This is the server side check to prevent ACF field groups from being saved on production.
 * The `wp_insert_post_empty_content` hook is usually used to prevent empty posts from being saved.
 * Here, we are using it to prevent ACF field groups from being saved on production,
 * by running `wp_die` to interrupt the process.
 * 
 * @see https://developer.wordpress.org/reference/hooks/wp_insert_post_empty_content/
 * 
 * @param bool $maybe_empty Whether the post is empty.
 * @param array $postarr The post data array.
 */
function acfFilterFieldGroupEditsServerSide(bool $maybe_empty, array $postarr): bool
{
    if ($postarr['post_type'] !== 'acf-field-group' && getenv('WP_ENV') !== 'development') {
        // If we are on production, we want to prevent the field group from being saved.
        wp_die('You are not allowed to update ACF field groups on production.');
    }

    return $maybe_empty;
}

add_filter('wp_insert_post_empty_content', 'acfFilterFieldGroupEditsServerSide', 10, 2);


/**
 * Allow ACF settings screens only on blog one.
 * 
 * This filter will prevent ACF settings screens from being accessed on any blog other than blog one.
 * 
 * @see https://www.advancedcustomfields.com/resources/acf-settings/
 */

function acfSettingsOnlyOnBlogOne( $capability ) {
    if (is_multisite() && get_current_blog_id() !== 1) {
        return 'do_not_allow';
    }
    return $capability;
}

add_filter('acf/settings/capability', 'acfSettingsOnlyOnBlogOne');


/**
 * Add the 'Site' location rule type to ACF field groups.
 * 
 * Part 1️⃣/3️⃣
 * 
 * @see https://www.advancedcustomfields.com/resources/custom-location-rules/
 * @see https://support.advancedcustomfields.com/forums/topic/multisite-location-rules-display-field-groups-on-specific-sites/
 * 
 * @param array $choices The location rule types.
 */
function acfLocationRuleTypeMultisite($choices)
{
    if(!is_multisite()) {
        return $choices;
    }
    
    $choices['Multisite']['site'] = 'Site';
    return $choices;
}

add_filter('acf/location/rule_types', 'acfLocationRuleTypeMultisite');


/**
 * Add the values for the 'Site' location rule type to ACF field groups.
 * 
 * Part 2️⃣/3️⃣
 * 
 * @param array $choices The location rule values.
 */
function acfLocationRuleValuesMultisite($choices)
{
    $choices['all'] = 'All';
    $sites = get_sites();

    foreach ($sites as $site) {
        $choices[get_object_vars($site)["blog_id"]] = get_object_vars($site)["domain"];
    }

    return $choices;
}

add_filter('acf/location/rule_values/site',  'acfLocationRuleValuesMultisite');


/**
 * Match the 'Site' location rule type to the current blog id.
 * 
 * Part 3️⃣/3️⃣
 * 
 * @param bool $match Whether the rule matches.
 * @param array $rule The location rule.
 */
function acfLocationRuleMatchMultisite($match, $rule)
{
    $current_site = get_current_blog_id();
    $selected_site = (int) $rule['value'];

    if ($rule['operator'] == "==") {
        $match = ($current_site == $selected_site);
    } elseif ($rule['operator'] == "!=") {
        $match = ($current_site != $selected_site);
    }

    return $match;
}

add_filter('acf/location/rule_match/site', 'acfLocationRuleMatchMultisite', 10, 2);
