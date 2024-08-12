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
        add_action('add_meta_boxes_attachment', [$this, 'maybeRemoveMetaBoxesAttachment'], 99, 0);
    }

    /**
     * Determine if the current user should have a restricted view.
     * In this context, only administrators can view Offload Media details.
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

    /**
     * On the Media list view page, remove columns if not an administrator
     *
     * @param $columns
     *
     * @return mixed
     */
    public function maybeRemoveColumns($columns): mixed
    {
        if ($this->view_restricted) {
            unset($columns['as3cf_bucket']);
            unset($columns['as3cf_access']);
        }

        return $columns;
    }

    /**
     * When viewing an edit page of post type attachment, remove
     * the Offload Media meta-box if user has restricted view.
     *
     * @return void
     */
    public function maybeRemoveMetaBoxesAttachment(): void
    {
        if ($this->view_restricted) {
            remove_meta_box('s3-actions', 'attachment', 'side');
        }
    }
}
