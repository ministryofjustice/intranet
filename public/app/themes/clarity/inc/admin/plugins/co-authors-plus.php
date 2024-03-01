<?php

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
                $fields_to_delete = [ 'yim', 'aim', 'jabber', 'yahooim', 'website' ];

                if (in_array($field['key'], $fields_to_delete)) {
                    unset($fields_to_return[ $index ]);
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
        $fields_to_delete = [ 'yim', 'aim', 'jabber', 'yahooim', 'website' ];

        foreach ($fields_to_delete as $field) {
            unset($contactmethods[ $field ]);
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
}
