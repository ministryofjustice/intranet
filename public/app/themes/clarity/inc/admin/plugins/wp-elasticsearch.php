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

    const RECENT_WEIGHT_POST_TYPES = ['blog', 'event', 'news', "note-from-antonia"];

    public function __construct()
    {
        // do early stuff here, outside WP ecosys...

        // load hooks here, inside WP ecosys...
        $this->hooks();
    }

    public function hooks(): void
    {
        add_filter('ep_search_post_return_args', [$this, 'removeUnsetFields']);

        add_filter('ep_formatted_args', [$this, 'removeFilterWeightRecent'], 12, 2);
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

    /**
     * Apply date based decay to only some post types.
     * 
     * @param  array $formatted_args Formatted ES args
     * @param  array $args WP_Query args
     * @return array
     */

    public function removeFilterWeightRecent($formatted_args, $args)
    {
        // Infer if date based decay is enabled by the shap of $formatted_args.
        if (empty($formatted_args['query']['function_score']['functions'])) {
            // 'Weighting by date' is off in Dashboard > ElasticPress > Features.
            return $formatted_args;
        }

        /**
         * Filter search date weighting scale
         * 
         * This function is copy/pasted from ElasticPress. 
         * Using it, ensures we are targeting the correct array key.
         *
         * @hook epwr_decay_function
         * @param  {string} $decay_function Current decay function
         * @param  {array} $formatted_args Formatted Elasticsearch arguments
         * @param  {array} $args WP_Query arguments
         * @return  {string} New decay function
         */
        $decay_name = apply_filters('epwr_decay_function', 'exp', $formatted_args, $args);

        // Map over the functions.
        $formatted_args['query']['function_score']['functions'] = array_map(function ($f) use ($decay_name) {
            // Identify the decay function.
            if (isset($f[$decay_name])) {
                // Add a filter so that the function will only apply to specific post types.
                $f['filter'] = [
                    'terms' => [
                        "post_type.raw" => $this::RECENT_WEIGHT_POST_TYPES
                    ]
                ];
            }
            return $f;
        }, $formatted_args['query']['function_score']['functions']);

        return $formatted_args;
    }
}
