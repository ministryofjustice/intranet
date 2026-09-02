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

        add_action('pre_get_posts', [$this, 'disableCacheResults'], 6);
    }

    /**
     * Stop ElasticPress-served posts from poisoning the WordPress post cache.
     *
     * ES-served posts are built from the Elasticsearch _source, so any column
     * missing from it falls back to the WP_Post default. `post_parent` is one:
     * MOJElasticSearch\ElasticPressHooks::removePostArgs unsets it before
     * indexing, so every ES-served post arrives with `post_parent = 0`. Same
     * goes for the other columns stripped there and in self::REMOVE_FIELDS.
     *
     * With `cache_results` on, WP_Query hands those objects to
     * update_post_caches(), which adds them to the persistent `posts` group.
     * A later get_permalink() then builds a URL with no ancestor segments on
     * pages that never went near search - a 404, or a link to the wrong page
     * where the truncated path matches a real top-level one.
     *
     * ElasticPress did this itself up to 5.3.2, then dropped it in 5.3.3 to
     * cache term and meta queries. That trade is unsafe while the index is
     * missing columns, so this restores the old behaviour and its opt-in. The
     * cost is re-priming term and meta caches per post on results pages.
     *
     * Index `post_parent` and this can go, along with the wrong values still
     * read straight off posts in the search loop, which this does not fix.
     *
     * @see https://github.com/10up/ElasticPress/blob/5.3.2/includes/classes/Indexable/Post/QueryIntegration.php#L119-L127
     *
     * @param \WP_Query $query The query about to run.
     * @return void
     */
    public function disableCacheResults($query): void
    {
        if (!class_exists('\\ElasticPress\\Indexables')) {
            return;
        }

        $indexable = \ElasticPress\Indexables::factory()->get('post');

        if (!$indexable || !$indexable->elasticpress_enabled($query)) {
            return;
        }

        if (apply_filters('ep_skip_query_integration', false, $query)) {
            return;
        }

        // Off by default, but honour an explicit opt-in from the caller.
        $query->set('cache_results', !empty($query->query['cache_results']));
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
