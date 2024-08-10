<?php
/**
 * Modifications to adapt the wp-offload-media plugin.
 *
 * @package Clarity
 **/
namespace MOJ\Intranet;

class WPOffloadMedia
{
    public function __construct()
    {
        $this->hooks();
    }

    public function hooks(): void
    {
        add_filter('manage_media_columns', [$this, 'ManageMediaColumns'], 99, 1);
    }

    public function ManageMediaColumns($columns)
    {
        $user = wp_get_current_user();

        if (!user_can($user->ID, 'administrator')) {
            unset($columns['as3cf_bucket']);
            unset($columns['as3cf_access']);
        }

        return $columns;
    }
}