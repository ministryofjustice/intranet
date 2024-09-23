<?php

/**
 * Modifications to adapt the ElasticPress plugin.
 *
 * @package Clarity
 **/

namespace MOJ\Intranet;

class WPElasticPress
{

    const REMOVE_FIELDS = [
        'post_author',
        'comment_count',
        'comment_status',
        'ping_status',
        'menu_order',
    ];

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

    /**
     * Filter post object fields/properties
     *
     * @param string[] $properties Post properties
     * @return string[] Filtered properties
     */
    public function removeUnsetFields($properties)
    {
        $properties = array_filter($properties, fn($p) => !in_array($p, $this::REMOVE_FIELDS));

        return $properties;
    }
}
