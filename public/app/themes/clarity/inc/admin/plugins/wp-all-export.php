<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

/**
 * How to filter the wp-all-export output for stakeholder export requests.
 * 
 * - Import an upto date copy of the production to localhost.
 * - Set memory_limit=512M for the local/php-fpm.php.
 * - Install wp-all-export locally.
 * - Disable WP Media Offload Plugin - so urls are not filtered.
 * - Include this file in functions.php and adjust the filter to your needs..
 * - Set environment variables accordingly, e.g. `WP_ALL_EXPORT_PARENT_ID`.
 * - Set the export WP_Query to: 'post_type' => 'page', 'post_status' => 'publish'.
 * - Set: Fields ID, Title, Content, URL, MenuOrder, Date.
 * - Increase the batch size so that a single batch covers all results, this allows us to sort by URL.
 * - Download the csv.
 * - Check local urls in content work. Search for 'docker'.
 * - Deactivate and uninstall wp-all-export.
 */

class WpAllExport
{

    public function __construct()
    {
        add_filter('wp_all_export_csv_rows', [$this, 'customOperationsAreaFilter']);
    }

    /**
     * This is an example of a customised function to filter the output oft the wp-all-export plugin.
     * 
     * @param array $articles the rows being exported.
     * 
     * @return array the filtered and mapped rows.
     */

    public function customOperationsAreaFilter($articles)
    {

        $parent_id = $_ENV['WP_ALL_EXPORT_PARENT_ID'];

        // Filter the rows, based on the parent id.
        $articles = array_filter($articles, function ($article) use ($parent_id) {

            $hit = get_post($article['id']);

            // Loop through all the posts found.
            if ($hit->ID === $parent_id) {
                // The page itself.
                return true;
            } elseif ($hit->post_parent === $parent_id) {
                // A direct descendant.
                return true;
            } elseif ($hit->post_parent > 0) {
                $ancestors = get_post_ancestors($hit);
                if (in_array(intval($parent_id), $ancestors, true)) {
                    // One of the lower level descendants.
                    return true;
                }
            }

            return false;
        });

        // Sort by URL.
        usort($articles, fn ($a, $b) => strcmp($a['URL'], $b['URL']));

        // Replace local url with production url.
        $home = get_home_url();
        $production_url = 'https://intranet.justice.gov.uk';

        $articles = array_map(function ($a) use ($home, $production_url) {
            $a['content'] = str_replace($home, $production_url, $a['content']);
            $a['URL'] = str_replace($home, $production_url, $a['URL']);
            return $a;
        }, $articles);

        return $articles;
    }
}

new WpAllExport();
