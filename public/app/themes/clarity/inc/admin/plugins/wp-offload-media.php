<?php
/**
 * Modifications to adapt the wp-offload-media plugin.
 *
 * @package Clarity
 **/
namespace MOJ\Intranet;

class WPOffloadMedia
{
    /**
     * Should we restrict the view for the current user?
     *
     * @var bool
     */
    var bool $view_restricted = false;

    public function __construct()
    {
        // do early stuff here, outside WP ecosys...

        // load hooks here, inside WP ecosys...
        $this->hooks();
    }

    public function hooks(): void
    {
        add_action('init', [$this, 'cannotView']);
        add_filter('manage_media_columns', [$this, 'maybeRemoveColumns'], 99, 1);
        add_action('add_meta_boxes_attachment', [$this, 'maybeRemoveMetaBoxes'], 99, 1);
    }

    /**
     * Determine if the current user should have a restricted view
     *
     * @return void
     */
    public function cannotView(): void
    {
        $user = wp_get_current_user();

        if (!user_can($user->ID, 'administrator')) {
            $this->view_restricted = true;
        }
    }

    public function maybeRemoveColumns($columns)
    {
        if ($this->view_restricted) {
            unset($columns['as3cf_bucket']);
            unset($columns['as3cf_access']);
        }

        return $columns;
    }

    public function maybeRemoveMetaBoxes($post): void
    {
        if ($this->view_restricted) {
            remove_meta_box('s3-actions', 'attachment', 'side');
        }
    }
}
