<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

require_once 'traits/constants.php';
require_once 'traits/page-content.php';
require_once 'traits/routes.php';
require_once 'traits/user.php';
require_once 'traits/utils.php';

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * Synergy Feed API
 *
 * This file contains the API for the Synergy feed.
 * It is used to serve content regarding:
 * - HR
 * - Finance & Commercial policy
 * - Guidance information
 */
class SynergyFeedApi
{
    use Constants;
    use PageContent;
    use Routes;
    use User;
    use Utils;

    public $agencies = [];

    public $content_types = [];

    public $feeds_response = [];


    public function __construct()
    {
        // Initialise the agencies and content types properties.
        $this->initProperties();

        // Register the REST API routes.
        add_action('rest_api_init', [$this, 'registerRoutes']);

        // Allow a subset of users to create an application password for themselves.
        add_filter('rest_authentication_errors', [$this, 'allowUserRestRouteForAdmins'], 11);
    }


    /**
     * Initialise the properties of the SynergyFeedApi class.
     * 
     * This method populates the `agencies` and `content_types` properties with the values from the `BASE_URIS` constant.
     * 
     * @return void
     */
    public function initProperties(): void
    {
        // Initialise the feeds endpoint response, with some default values.
        $this->feeds_response = [
            'timestamp' => date(\DateTime::ATOM),
            'items_count' => 0,
            'items' => [],
        ];

        foreach ($this::BASE_URIS as $uri => $data) {
            // Add the agencies to the enum for the 'agency' parameter in the REST API route.
            foreach ($data['agencies'] as $agency) {
                if (!in_array($agency, $this->agencies)) {
                    $this->agencies[] = $agency;
                }
            }

            // Add the content type to the enum for the 'content_type' parameter in the REST API route.
            if (!in_array($data['content_type'], $this->content_types)) {
                $this->content_types[] = $data['content_type'];
            }

            // Add this entry to the feeds response.
            $this->feeds_response['items'][] = [
                'feed_api_url' => get_home_url(null, '/wp-json/synergy/v1/feed?' . http_build_query([
                    'agency' => $data['agencies'][0],
                    'content_type' => $data['content_type'],
                ])),
                'base_permalink' => get_home_url(null, $uri),
                ...$data,
            ];
        }

        // Count the items in the feeds response.
        $this->feeds_response['items_count'] = count($this->feeds_response['items']);
    }


    /**
     * A helper function to get the base URIs from the properties.
     * 
     * This function checks the `BASE_URIS` constant for a matching agency and content type,
     * and returns the base URI if found.
     * 
     * @param string $agency The agency to match.
     * @param string $content_type The content type to match.
     * @return array|null The base URI if found, null otherwise.
     */
    public function getBaseUrisFromProperties($agency, $content_type): array|null
    {
        // Find the base URI that matches the agency and content type.
        $filtered_uris = array_filter(
            $this::BASE_URIS,
            function ($base_uri) use ($agency, $content_type) {
                return in_array($agency, $base_uri['agencies']) && $content_type === $base_uri['content_type'];
            }
        );

        // If no matching base URI is found, return an empty string.
        if (empty($filtered_uris)) {
            return null;
        }

        // Return the first matching base URI.
        return array_keys($filtered_uris);
    }


    /**
     * Get the pages for the Synergy API.
     * 
     * This function retrieves the feed for the Synergy API based on the provided parameters.
     * It returns an array containing the matching pages.
     * 
     * @param WP_REST_Request $request The request object containing the parameters.
     * @return array The response data containing the feed items.
     * @throws WP_Error If the page is not found or if there is an error in the request.
     */
    public function getFeed(WP_REST_Request $request): array
    {
        $format = $request->get_param('format');

        $modified_after = $request->get_param('modified_after');

        $agency = $request->get_param('agency');

        $content_type = $request->get_param('content_type');

        $base_uris = $this->getBaseUrisFromProperties($agency, $content_type);

        // Construct a request ID, this will be used in the CSV filename to help identify the request.
        $request_id = "{$agency}_{$content_type}";

        if($modified_after) {
            $request_id .= '_modified-after-' . str_replace(':', '-', $modified_after);
        }

        $data = [
            'request_id' => $request_id,
            'format' => $format,
            'modified_after' => $modified_after,
            'agency' => $agency,
            'content_type' => $content_type,
            'base_permalinks' => array_map(fn($uri) => get_home_url(null, $uri), $base_uris),
            'timestamp' => date(\DateTime::ATOM),
            'items_count' => 0,
            'items' => [],
        ];

        // Loop over the base URIs.
        foreach ($base_uris as $base_uri) {
            // Get the page with the root URI.
            $page = get_page_by_path($base_uri, OBJECT, 'page');

            if (!$page) {
                throw new WP_Error(
                    'page_not_found',
                    'Page not found for the provided base URI.',
                    ['status' => 404]
                );
            }

            // Is there a modified_after parameter is this page modified after the provided date?
            if (!$modified_after || strtotime($page->post_modified) > strtotime($modified_after)) {
                // If it is, then format the page and add it to the response.
                $root_page_formatted = $this->formatPagePayload($page, $agency, $content_type, $format);
                $data['items'][] = $root_page_formatted;
            }

            // Get all descendants of the page, optionally filtered by modified date.
            $descendants = $this->getAllDescendants(page_id: $page->ID, modified_after: $modified_after);

            // Map over the descendants and format them.
            $descendants_formatted = array_map(function ($descendant) use ($format, $agency, $content_type) {
                return $this->formatPagePayload($descendant, $agency, $content_type, $format);
            }, $descendants);

            // Add the formatted descendants to the response.
            array_push($data['items'], ...$descendants_formatted);
        }

        // Loop over the pages, and add documents to the items.
        foreach ($data['items'] as &$item) {
            // Get the documents from the content of the page.
            $document_ids = $this->getDocumentsFromContent(get_home_url(), $item['content']);

            // Add document_ids to a linked documents column
            $item['linked_ids'] = $document_ids;

            // If there are documents, add them to the item.
            // if (!empty($document_ids)) {
            //     $document_id = 551396;
            //     $document = get_post($document_id);
            //     $data['items'][] = $this->formatPagePayload(
            //         $document,
            //         $agency,
            //         $content_type,
            //         $format
            //     );
            // }
        }

        // Count the items in the response.
        $data['items_count'] = count($data['items']);

        return $data;
    }


    /**
     * Get the pages for the Synergy API - JSON response.
     * 
     * This function is a wrapper around the getFeed function.
     * It handles the response and error handling for the REST API.
     * It returns a WP_REST_Response object containing the feed data or an error message.
     * 
     * @param WP_REST_Request $request The request object containing the parameters.
     * @return WP_REST_Response The response object containing the feed data.
     */
    public function getFeedJson(WP_REST_Request $request): WP_REST_Response
    {
        try {
            $data = $this->getFeed($request);
            // Return a WP_REST_Response with the data and a 200 status code.
            return new WP_REST_Response($data, 200);
        } catch (WP_Error $e) {
            // If an error occurs, return a WP_REST_Response with the error data.
            return new WP_REST_Response([
                'error' => $e->get_error_message(),
                'code' => $e->get_error_code(),
                'status' => $e->get_error_data()['status'] ?? 500,
            ], $e->get_error_data()['status'] ?? 500);
        }
    }


    /**
     * Get the pages for the Synergy API - CSV response.
     *
     * This function retrieves the feed for the Synergy API and outputs it as a CSV file.
     * It sets the appropriate headers for a CSV download and writes the feed data to the output.
     *
     * @param WP_REST_Request $request The request object containing the parameters.
     * @return string The CSV data as a string.
     */
    public function getFeedCsv(WP_REST_Request $request): string
    {
        try {
            $data = $this->getFeed($request);

            // Set the headers for the CSV download.
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="synergy_feed_' .$data['request_id'] . '.csv"');

            // Stream the CSV data to the browser/client.
            $out = fopen('php://output', 'w');

            // Write the header row.
            fputcsv($out, [
                self::CSV_HEADERS['id'],
                self::CSV_HEADERS['title'],
                self::CSV_HEADERS['agency'],
                self::CSV_HEADERS['additional_agencies'],
                self::CSV_HEADERS['content_type'],
                self::CSV_HEADERS['status'],
                self::CSV_HEADERS['location'],
                self::CSV_HEADERS['format'],
                self::CSV_HEADERS['url'],
                self::CSV_HEADERS['author'],
                self::CSV_HEADERS['additional_authors'],
                self::CSV_HEADERS['published'],
                self::CSV_HEADERS['modified'],
            ]);

            // Write each item in the feed to the CSV.
            foreach ($data['items'] as $item) {
                fputcsv($out, [
                    $item['id'],
                    $item['title'],
                    $item['agency'],
                    implode(',', $item['additional_agencies']),
                    $item['content_type'],
                    self::CSV_STATUSES[$item['status']] ?? $item['status'],
                    $item['location'],
                    'Web Page',
                    $item['url'],
                    $item['author'],
                    implode(',', $item['additional_authors']),
                    $item['published'],
                    $item['modified'],
                ]);
            }

            fclose($out);
        } catch (WP_Error $e) {
            header('Content-Type: application/json');
            http_response_code($e->get_error_data()['status'] ?? 500);
            echo json_encode([
                'error' => $e->get_error_message(),
                'code' => $e->get_error_code(),
                'status' => $e->get_error_data()['status'] ?? 500,
            ]);
            exit;
        }

        exit;
    }

    /**
     * Modify the query arguments for get_pages to include a date_query.
     * 
     * This is necessary because get_pages does not support date_query by default.
     * 
     * @param array $query_args The query arguments for get_pages.
     * @param array $passed_args The arguments passed to the get_pages function.
     * @return array The modified query arguments.
     */
    public function modifyGetPagesQueryArgs($query_args, $passed_args)
    {
        $query_args['date_query'] = $passed_args['date_query'] ?? [];

        return $query_args;
    }


    /**
     * Get all descendants of a page, optionally filtered by modified date.
     * 
     * @param int $page_id The ID of the page to get descendants for.
     * @param string|null  $modified_after Optional. A date in ISO 8601 format to filter descendants by their last modified date.
     * @return array An array of page objects representing the descendants.
     */
    public function getAllDescendants(int $page_id, string|null $modified_after): array
    {
        $get_pages_args = [
            'child_of' => $page_id,
            'sort_column' => 'menu_order',
            'sort_order' => 'ASC',
            'post_type' => 'page',
            // Optionally add a date query to filter by modified date.
            ...(!empty($modified_after) ? ['date_query' => [
                'column' => 'post_modified',
                'after' => $modified_after,
            ]] : [])
        ];

        // Add a filter to modify the query args for get_pages.
        // This is necessary to ensure that the date_query is applied correctly.
        add_filter('get_pages_query_args', [$this, 'modifyGetPagesQueryArgs'], 10, 2);

        $descendants = get_pages($get_pages_args);

        // Remove the filter after we have retrieved the pages.
        // This is important to avoid affecting other queries that use get_pages.
        remove_filter('get_pages_query_args', [$this, 'modifyGetPagesQueryArgs'], 10, 2);

        return $descendants;
    }


    /**
     * Format the page payload for the Synergy feed.
     * 
     * This function formats the page payload to include the necessary information.
     * 
     * @param object $page The page object to format.
     * @param string $format The preferred format to return the content in, either 'html' or 'markdown'.
     * @return array The formatted page payload.
     */
    public function formatPagePayload(\WP_Post $page, string $request_agency, string $content_type, string $format = 'markdown'): array
    {
        $page->post_content = $this->getPageContent($page, $format);

        // Authors - get the authors according to co-authors-plus plugin.
        $authors = get_coauthors($page->ID) ?? [];
        $author_names = array_map(fn($author) => $author->display_name, $authors);

        // Agencies - this is equivalent to the 'Content tagged as' part of the rendered page.
        $agencies = get_the_terms($page->ID, 'agency') ?: [];
        $agency_slugs = array_map(fn($agency) => $agency->slug, $agencies);

        // Determine the primary agency - default to the one provided in the request.
        $agency = $request_agency;

        if (in_array('hq', $agency_slugs)) {
            // If there is an 'hq' agency, then use that as the primary agency.
            // This is because other agencies 'borrow' content from HQ, 
            // but HQ does not borrow content from other agencies.
            $agency = 'hq';
        }

        // Build a human readable location string.
        $agency_term = get_term_by('slug', $agency, 'agency');
        $location = "MoJ Intranet - {$agency_term->name}";

        $formatted_page = [
            'id' => $page->ID,
            // Post title.
            'title' => $page->post_title,
            // Post excerpt, if it exists.
            'excerpt' => $page->post_excerpt,
            // Parent ID of the page, if it has a parent.
            'parent_id' => $page->post_parent,
            // Format post_date and post_modified to ISO 8601 format.
            'published' => date(\DateTime::ATOM, strtotime($page->post_date)),
            'modified' => date(\DateTime::ATOM, strtotime($page->post_modified)),
            // Permalink for the page.
            'url' => get_permalink($page->ID),
            // Primary author in human readable format.
            'author' => $author_names[0] ?? '',
            // Additional authors, if any, are those in the authors array excluding the primary author.
            'additional_authors' => array_filter($author_names, fn($name) => $name !== $author_names[0]),
            // Format the content according to the requested format.
            // Author information.
            'authors' => array_map(fn($author) => [
                'ID' => $author->ID,
                'display_name' => $author->display_name,
                'user_nicename' => $author->user_nicename,
            ], $authors),
            // Primary agency, if it exists.
            'agency' => $agency,
            // All tagged agencies
            'tagged_agencies' => $agency_slugs,
            // Additional agencies, if any, are those tagged in the content excluding the primary agency.
            'additional_agencies' => array_filter($agency_slugs, fn($slug) => $slug !== $agency),
            // Content type, e.g. 'hr', 'finance', 'commercial', 'guidance'.
            'content_type' => $content_type,
            // Menu order may be useful in working out hierarchy or order of pages.
            'menu_order' => $page->menu_order,
            // Location - a human readable string that describes the location of the page.
            'location' => $location,
            // Less important properties...
            'status' => $page->post_status,
            'type' => $page->post_type,
            // Finally, add the content to the page object.
            'content' => $page->post_content,
        ];

        return $formatted_page;
    }
}

new SynergyFeedApi();
