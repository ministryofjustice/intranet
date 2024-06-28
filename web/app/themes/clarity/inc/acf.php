<?php

use MOJ\Intranet;

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

/**
 * Create potions page for prior political parties
 */
add_action('init', 'prior_party_option_pages');

function prior_party_option_pages()
{
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(
            array(
                'page_title' => 'Prior Political Parties',
                'capability' => 'manage_options',
                'icon_url' => 'dashicons-calendar',
                'menu_title' => 'Prior Political Parties',
                'menu_slug' => 'prior-party-settings',
                'parent_slug' => 'tools.php',
            )
        );
    }
}

add_filter('acf/load_field', 'home_option_fields');

function home_option_fields($field)
{
    $screen = get_current_screen();

    $agency_wide_fields = ['enable_agency_wide_banner', 'agency_wide_banner_title', 'agency_wide_banner_message', 'agency_wide_banner_type', 'agency_wide_banner_link'];

    if (isset($screen) && ($screen->id == 'toplevel_page_homepage-settings')) {

        $context = Agency_Context::get_agency_context();

        if (!in_array($field['name'], $agency_wide_fields)) {
            $field['name'] = $context . '_' . $field['name'];
        }


    }
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

function header_option_fields($field)
{
    $screen = get_current_screen();

    if (isset($screen) && ($screen->id == 'toplevel_page_header-settings')) {
        $context = Agency_Context::get_agency_context();
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
function my_admin_only_load_field($field)
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

add_filter('acf/load_field', 'my_admin_only_load_field');


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
