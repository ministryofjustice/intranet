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

    public $agencies = ['all'];

    public $content_types = ['all'];

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
            // Add the agency to the enum for the 'agency' parameter in the REST API route.
            if (!in_array($data['agency'], $this->agencies)) {
                $this->agencies[] = $data['agency'];
            }

            // Add the content type to the enum for the 'content_type' parameter in the REST API route.
            if (!in_array($data['content_type'], $this->content_types)) {
                $this->content_types[] = $data['content_type'];
            }

            $query = http_build_query([
                'agency' => $data['agency'],
                'content_type' => $data['content_type'],
            ]);

            // Add this entry to the feeds response.
            $this->feeds_response['items'][] = [
                'feed_csv' => get_home_url(null, '/wp-json/synergy/v1/feed.csv?' . $query),
                'feed_json' => get_home_url(null, '/wp-json/synergy/v1/feed?' . $query),
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
     * @return array|null The base URI array(s) if found, null otherwise.
     */
    public function getBaseUrisFromProperties($agency, $content_type): array|null
    {
        // Find the base URI that matches the agency and content type.
        $filtered_uris = array_filter(
            $this::BASE_URIS,
            function ($base_uri) use ($agency, $content_type) {
                $agency_match = 'all' ===  $agency || $agency === $base_uri['agency'];
                $content_type_match = 'all' === $content_type || $content_type === $base_uri['content_type'];
                return $agency_match && $content_type_match;
            }
        );

        // If no matching base URI is found, return an empty string.
        if (empty($filtered_uris)) {
            return null;
        }

        // Return the matching base URI array(s).
        return $filtered_uris;
    }


    /**
     * Get the pages for the Synergy API.
     * 
     * This function retrieves the feed for the Synergy API based on the provided parameters.
     * It returns an array containing the matching pages.
     * 
     * @param WP_REST_Request $request The request object containing the parameters.
     * @return array|WP_Error The response data containing the feed items.
     */
    public function getFeed(WP_REST_Request $request): array|WP_Error
    {
        $format = $request->get_param('format');

        $modified_after = $request->get_param('modified_after');

        $agency = $request->get_param('agency');

        $content_type = $request->get_param('content_type');

        $base_uris = $this->getBaseUrisFromProperties($agency, $content_type);

        // Construct a request ID, this will be used in the CSV filename to help identify the request.
        $request_id = "{$agency}_{$content_type}";

        if ($modified_after) {
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
        foreach ($base_uris as $base_uri => $base_uri_values) {

            // Get the content type for the current iteration, since the request could be for 'all' content types.
            $content_type = $base_uri_values['content_type'];

            // Get the agency for the current iteration, since the request could be for 'all' agencies.
            $agency = $base_uri_values['agency'];

            // Get the page with the root URI.
            $page = get_page_by_path($base_uri, OBJECT, 'page');

            if (!$page) {
                return new WP_Error(
                    'page_not_found',
                    'Page not found for the provided base URI.',
                    ['status' => 404]
                );
            }

            // Start off the breadcrumbs with this base page, we don't need to start with Home and Guidance.
            $breadcrumbs_from_parent = [['title' => $page->post_title, 'id' => $page->ID]];

            // Format the page and add it to the response.
            $data['items'][] = $this->formatPagePayload($page, $agency, $content_type, $format, $breadcrumbs_from_parent);

            // Get all descendants of the page.
            $descendants = $this->getAllDescendants($page->ID);

            // Map over the descendants and format them.
            $descendants_formatted = array_map(function ($descendant) use ($format, $agency, $content_type, $breadcrumbs_from_parent) {
                return $this->formatPagePayload($descendant, $agency, $content_type, $format, $breadcrumbs_from_parent);
            }, $descendants);

            // Add the formatted descendants to the response.
            array_push($data['items'], ...$descendants_formatted);
        }

        global $wpdr;
        $documents_formatted = [];

        // Loop over the pages, and add documents to the items.
        foreach ($data['items'] as &$item) {
            // Get the documents from the content of the page.
            $document_ids = $this->getDocumentsFromContent(get_home_url(), $item['content']);

            // Add document_ids to a linked documents column
            $item['linked_ids'] = $document_ids;

            $breadcrumbs_from_parent = $item['breadcrumbs'];

            foreach ($document_ids as $document_id) {
                $exists_in_document_formatted = $this->arrayFind(
                    $documents_formatted,
                    fn($i) => $i['id'] === $document_id
                );

                if ($exists_in_document_formatted) {
                    // If the document already exists in the formatted array, skip it.
                    continue;
                }

                // Get the document post object.
                $document = get_post($document_id);

                if (!$document) {
                    // If the document does not exist, skip to the next iteration.
                    continue;
                }

                if (!$document->post_status || $document->post_status !== 'publish') {
                    // If the document is not published, skip to the next iteration.
                    continue;
                }

                if (!$wpdr->get_document($document->ID)) {
                    // If the document is not found in WP Document Revisions, skip to the next iteration.
                    // This is an edge case where a document has no attachment.
                    continue;
                }

                if (!$modified_after || strtotime($document->post_modified) > strtotime($modified_after)) {
                    $documents_formatted[] = $this->formatPagePayload(
                        $document,
                        $item['agency'],
                        $item['content_type'],
                        $format,
                        $breadcrumbs_from_parent
                    );
                }
            }
        }

        // Filter $data['items'] to remove pages that were not modified after $modified_after.
        if ($modified_after) {
            $data['items'] = array_filter($data['items'], function ($item) use ($modified_after) {
                return strtotime($item['modified']) > strtotime($modified_after);
            });
        }

        // Add the documents to the items.
        $data['items'] = array_merge($data['items'], $documents_formatted);

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
        $data = $this->getFeed($request);

        if (is_wp_error($data)) {
            // If an error occurred, return a WP_REST_Response with the error data.
            return new WP_REST_Response([
                'error' => $data->get_error_message(),
                'code' => $data->get_error_code(),
                'status' => $data->get_error_data()['status'] ?? 500,
            ], $data->get_error_data()['status'] ?? 500);
        }

        // Return a WP_REST_Response with the data and a 200 status code.
        return new WP_REST_Response($data, 200);
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
        $data = $this->getFeed($request);

        if (is_wp_error($data)) {
            // If an error occurred, return a JSON response with the error data.
            header('Content-Type: application/json');
            http_response_code($data->get_error_data()['status'] ?? 500);
            echo json_encode([
                'error' => $data->get_error_message(),
                'code' => $data->get_error_code(),
                'status' => $data->get_error_data()['status'] ?? 500,
            ]);
            exit;
        }

        try {
            // Set the headers for the CSV download.
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="synergy_feed_' . $data['request_id'] . '.csv"');

            // Stream the CSV data to the browser/client.
            $out = fopen('php://output', 'w');

            // Write the header row.
            fputcsv($out, [
                self::CSV_HEADERS['id'],
                self::CSV_HEADERS['linked_ids'],
                self::CSV_HEADERS['title'],
                self::CSV_HEADERS['agency'],
                self::CSV_HEADERS['additional_agencies'],
                self::CSV_HEADERS['content_type'],
                self::CSV_HEADERS['category'],
                self::CSV_HEADERS['status'],
                self::CSV_HEADERS['location'],
                self::CSV_HEADERS['file_type'],
                self::CSV_HEADERS['url'],
                self::CSV_HEADERS['author'],
                self::CSV_HEADERS['additional_authors'],
                self::CSV_HEADERS['published'],
                self::CSV_HEADERS['modified'],
            ]);

            // Write each item in the feed to the CSV.
            foreach ($data['items'] as $item) {
                if (sizeof($item['breadcrumbs']) > 1) {
                    array_splice($item['breadcrumbs'], -1);
                }
                $categories = $item['breadcrumbs'] ? array_map(fn($b) => $b['title'], $item['breadcrumbs']) : [];
                fputcsv($out, [
                    $item['id'],
                    implode(', ', $item['linked_ids'] ?? []),
                    $item['title'],
                    $item['agency'],
                    implode(', ', $item['additional_agencies']),
                    $item['content_type'],
                    implode(' > ', $categories),
                    self::CSV_STATUSES[$item['status']] ?? $item['status'],
                    $item['location'],
                    $item['file_type'],
                    $item['url'],
                    $item['author'],
                    implode(', ', $item['additional_authors']),
                    $item['published'],
                    $item['modified'],
                ]);
            }

            fclose($out);
        } catch (\Exception $e) {
            // If an error occurred while writing the CSV, return a JSON response with the error data
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'error' => 'An error occurred while generating the CSV file: ' . $e->getMessage(),
                'code' => 'csv_generation_error',
                'status' => 500,
            ]);
        }

        exit;
    }


    /**
     * Get all descendants of a page, optionally filtered by modified date.
     * 
     * @param int $page_id The ID of the page to get descendants for.
     * @return array An array of page objects representing the descendants.
     */
    public function getAllDescendants(int $page_id): array
    {
        $get_pages_args = [
            'child_of' => $page_id,
            'sort_column' => 'menu_order',
            'sort_order' => 'ASC',
            'post_type' => 'page',
        ];

        $descendants = get_pages($get_pages_args);

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
    public function formatPagePayload(\WP_Post $page, string $request_agency, string $content_type, string $format, array $breadcrumbs_from_parent): array
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

        $file_type = $format === 'markdown' ? 'md' : 'html';

        if ($page->post_type === 'document') {
            global $wpdr;
            $attach = $wpdr->get_document($page->ID);
            $file = get_attached_file($attach?->ID ?? 0);
            $file_type = pathinfo($file, PATHINFO_EXTENSION);
        }

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
            // Post type
            'type' => $page->post_type,
            // File type, if applicable.
            'file_type' => $file_type,
            // Category, i.e. the patent pages. e.g. HR or HR > Conduct and behaviour > Declarations of interest etc.
            'breadcrumbs' => self::getBreadcrumbs($breadcrumbs_from_parent, $page),
            // Finally, add the content to the page object.
            'content' => $page->post_content,
        ];

        return $formatted_page;
    }


    /**
     * Get the breadcrumbs for a page.
     *
     * @param array $existing_breadcrumbs The existing breadcrumbs to append to.
     * @param \WP_Post $page The current page to add to the breadcrumbs.
     * @return array The updated breadcrumbs array.
     */
    public static function getBreadcrumbs($existing_breadcrumbs, $page)
    {
        // If the last breadcrumb is the same as the current page, return the existing breadcrumbs.
        if (end($existing_breadcrumbs)['id'] === $page->ID) {
            return $existing_breadcrumbs;
        }

        // If the page is a document, we just need to add it to the breadcrumbs.
        if ($page->post_type === 'document') {
            return array_merge($existing_breadcrumbs, [['title' => $page->post_title, 'id' => $page->ID]]);
        }

        // Here, we are dealing with a page, not a document...
        // we need to get the parent pages and add them to the breadcrumbs.
        // we will add any intermediate pages to the breadcrumbs...
        // e.g. HR > Intermediate page 1 > Intermediate page 2 > Current page.

        // Let's start with the current page in the breadcrumbs.
        $reverse_breadcrumbs = [['title' => $page->post_title, 'id' => $page->ID]];

        // Get the page parent, if it exists.
        $parent = get_post($page->post_parent);

        // Loop while parent exists, and it is not the last breadcrumb.
        while ($parent && $parent->ID !== end($existing_breadcrumbs)['id']) {
            // If the parent exists, and it's not the last entry in the breadcrumbs array, append it.
            $reverse_breadcrumbs[] = ['title' => $parent->post_title, 'id' => $parent->ID];

            // Get the parent of the parent.
            $parent = get_post($parent->post_parent);
        }

        // Reverse the breadcrumbs to have the current page at the end.
        $forward_breadcrumbs = array_reverse($reverse_breadcrumbs);

        return array_merge($existing_breadcrumbs, $forward_breadcrumbs);
    }
}

new SynergyFeedApi();
