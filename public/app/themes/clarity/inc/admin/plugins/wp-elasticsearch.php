<?php
/**
 * Modifications to adapt the ElasticPress plugin.
 *
 * @package Clarity
 **/
namespace MOJ\Intranet;

class WPElasticPress
{
    public function __construct()
    {
        // do early stuff here, outside WP ecosys...

        // load hooks here, inside WP ecosys...
        $this->hooks();
    }

    public function hooks(): void
    {
        add_filter('ep_search_post_return_args', [$this, 'removeUnsetFields']);
    }

    public function removeUnsetFields($properties)
    {
        unset($properties['post_author']);
        unset($properties['comment_count']);
        unset($properties['comment_status']);
        unset($properties['ping_status']);
        unset($properties['menu_order']);

        return $properties;
    }
}
