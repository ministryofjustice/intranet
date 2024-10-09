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

        // The following code is a copy/paste from an ElasticPress query.
        // "functions": [
        //     {
        //         "exp": {
        //             "post_date_gmt": {
        //                 "scale": "14d",
        //                 "decay": 0.25,
        //                 "offset": "7d"
        //             }
        //         }
        //     },
        //     {
        //         "weight": 0.001
        //     }
        // ],

        // Remove it, so that we can use our own script_score.
        unset($formatted_args['query']['function_score']['functions']);

        /**
         * Apply a script_score to the query.
         * 
         * For post types: blog, event & news - apply a *severe* decay script to the score.
         * For other post types - apply a *mild* decay script to the score.
         * 
         * @see https://opensearch.org/docs/latest/query-dsl/specialized/script-score/#decay-functions
         * @see https://opensearch.org/docs/latest/query-dsl/compound/function-score/#decay-functions
         */

        $formatted_args['query']['function_score']["script_score"] = [
            "script" => [
                'source' => "
                    if (
                        doc['post_type.raw'].value == 'blog' 
                        || doc['post_type.raw'].value == 'event' 
                        || doc['post_type.raw'].value == 'news' 
                    ) {
                        return _score * decayDateExp(params.severe.origin, params.severe.scale,  params.severe.offset, params.severe.decay, doc.post_modified_gmt.value);
                    } else { 
                        return _score * decayDateExp(params.mild.origin, params.mild.scale,  params.mild.offset, params.mild.decay, doc.post_modified_gmt.value);
                    }",
                'params' => [
                    "severe"  => [
                        "scale" => "14d",
                        "decay" => 0.25, // the score to assign to a document at the scale + offset distance.
                        "offset" => "7d",
                        // Today's date in the format : strict_date_optional_time without time
                        "origin" => date('Y-m-d')
                    ],
                    "mild"  => [
                        "scale" => "183d",
                        "decay" => 0.6,
                        "offset" => "183d",
                        "origin" => date('Y-m-d')
                    ],
                ]
            ]
        ];

        return $formatted_args;
    }
}
