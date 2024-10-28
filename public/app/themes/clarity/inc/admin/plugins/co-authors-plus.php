<?php

use MOJ\Intranet\TransientAdminNotices;

/**
 * Modifications to adapt the co-authors-plus plugin to our Clarity theme.
 *
 * @package Clarity
 **/

if (function_exists('get_coauthors')) {

    /**
     * Adds plugin to API
     */
    add_action('rest_api_init', 'custom_register_coauthors');

    function custom_register_coauthors()
    {
        register_rest_field(
            'post',
            'coauthors',
            array(
                'get_callback'    => 'custom_get_coauthors',
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    function custom_get_coauthors($object, $field_name, $request)
    {
        $coauthors = get_coauthors($object['id']);

        $authors = array();
        foreach ($coauthors as $author) {
            $authors[] = array(
                'display_name'     => $author->display_name,
                'author_id'        => $author->ID,
                'thumbnail_avatar' => get_the_post_thumbnail_url($author->ID, 'intranet-large'),
            );
        };

        return $authors;
    }

    /**
     * Add additional fields to author profile
     *
     * @param array $fields_to_return Author profile fields
     * @param array $groups           Field groups
     */
    add_filter('coauthors_guest_author_fields', 'dw_add_author_fields', 10, 2);

    function dw_add_author_fields($fields_to_return, $groups)
    {
        if (in_array('all', $groups) || in_array('contact-info', $groups)) {
            $fields_to_return[] = [
                'key'   => 'job_title',
                'label' => 'Job Title',
                'group' => 'contact-info',
            ];

            foreach ($fields_to_return as $index => $field) {
                $fields_to_delete = ['yim', 'aim', 'jabber', 'yahooim', 'website'];

                if (in_array($field['key'], $fields_to_delete)) {
                    unset($fields_to_return[$index]);
                }
            }
        }
        return $fields_to_return;
    }

    /**
     * Remove unecessary contact methods from user profile
     *
     * @param  array $contactmethods Current contact methods
     * @return array                 Updated contact methods
     */
    add_filter('user_contactmethods', 'dw_edit_contactmethods', 10, 1);

    function dw_edit_contactmethods($contactmethods)
    {
        $fields_to_delete = ['yim', 'aim', 'jabber', 'yahooim', 'website'];

        foreach ($fields_to_delete as $field) {
            unset($contactmethods[$field]);
        }
        return $contactmethods;
    }

    /**
     * Allow editors to manage guest author profiles
     */
    add_filter('coauthors_guest_author_manage_cap', 'dw_filter_guest_author_manage_cap');

    function dw_filter_guest_author_manage_cap($cap)
    {
        return 'edit_others_posts';
    }

    /**
     * Checks if a local avatar has been selected by a user
     *
     * @param  string $url Current url of avatar
     * @param  string $url ID or Email of user
     * @param  array  $args Attributes of the avatar
     * @return string Url of avatar
     */
    add_filter('get_avatar_url', 'check_local_avatar', 99, 3);

    function check_local_avatar($url, $id_or_email, $args)
    {
        if (is_numeric($id_or_email)) {
            $local_avatar = get_user_meta($id_or_email, 'wp_user_avatar', true);

            if (is_numeric($local_avatar)) {
                $url = wp_get_attachment_image_src($local_avatar, 'user-thumb');

                // get a default avatar
                $avatar = '';
                if (!$url) {
                    $user = get_userdata($id_or_email);
                    if ($user) {
                        $avatar = 'https://www.gravatar.com/avatar/' . md5(strtolower($user->user_email)) . '?d=mp';
                    }
                }

                $url = $url[0] ?? $avatar;
            }
        }

        // always return an avatar
        return $url ?: 'https://www.gravatar.com/avatar/?d=mp';
    }

    /**
     * Filter wp_die_handler to use custom handler for when the post type is guest-author
     * 
     * This is necessary because the co-authors plugin uses wp_die to handle errors
     * and we need to override the default handler to understand what error happened.
     * Without this, the error message is not displayed and the user will see the static 500.html page.
     * 
     * @see https://github.com/Automattic/Co-Authors-Plus/issues/227 - Open issue to replace wp_die
     * @see https://developer.wordpress.org/reference/hooks/wp_die_handler/ - wp_die_handler hook
     * 
     * @param string $handler The current handler
     * @return string The new handler
     */

    add_filter('wp_die_handler', 'coauthors_filter_wp_die_handler');

    function coauthors_filter_wp_die_handler(string $handler): string
    {
        global $post;

        // If the post does not have an error and is a guest-author post type.
        if (!is_wp_error($post) && $post->post_type === 'guest-author') {
            return 'coauthors_wp_die_handler';
        }

        return $handler;
    }


    add_filter('gettext', 'coauthors_filter_text', 10, 3);

    /**
     * Filter the text of the plugin to remove the string 'WordPress'.
     * 
     * @see https://developer.wordpress.org/reference/hooks/gettext/
     * 
     * @param string $translated_text The translated text
     * @param string $text The original text
     * @param string $domain The text domain
     * @return string The modified text
     */

    function coauthors_filter_text(string $translated_text, string $text, string $domain): string
    {
        if ($domain === 'co-authors-plus') {
            // Remove the string 'WordPress' from the plugin's text.
            $translated_text = str_replace('WordPress user', 'user', $translated_text);
            $translated_text = str_replace('WordPress User Mapping', 'User Mapping', $translated_text);
        }
        
        return $translated_text;
    }

    /**
     * Custom handler for wp_die when the post type is guest-author
     * 
     * This function will either: 
     * - redirect to the referer url and add an admin notice with the error message.
     * - or, send the error message to Sentry and run the original wp_die handler.
     * 
     * @param string $message The error message
     * @param string $title The error title
     * @param array $args Additional arguments
     * @return void
     */

    function coauthors_wp_die_handler(string $message, string $title = '', array $args = array()): void
    {
        global $post;

        $user_id = get_current_user_id();

        $expected_referer = "/wp/wp-admin/post.php?post={$post->ID}&action=edit";

        // If a user is logged in , and the referer is the expected referer...
        if ($user_id && $expected_referer === wp_get_referer() && class_exists('MOJ\Intranet\TransientAdminNotices')) {
            // Create a new instance of the transient admin notices class
            $notice_transient = new TransientAdminNotices('theme_user_notice:' . $user_id);

            // Add the error to the notice queue.
            $notice_transient->add($title, $message, 'error');

            // Redirect to the referring page.
            wp_safe_redirect($expected_referer);
            die();
        }

        // Create a new WP_Error object with the error message.
        // In coauthors_filter_wp_die_handler, is_wp_error($post) will return true.
        // This is essential to prevent an infinite loop.
        $post = new WP_Error($message);

        // Send the error to Sentry.
        if (is_plugin_active('wp-sentry/wp-sentry.php')) {
            \Sentry\captureException(new Exception($message));
        }

        // Get the original die handler.
        $die_handler = apply_filters('wp_die_handler', '_default_wp_die_handler');

        // Call the original die handler.
        call_user_func($die_handler, $message, $title, $args);
    }
}
