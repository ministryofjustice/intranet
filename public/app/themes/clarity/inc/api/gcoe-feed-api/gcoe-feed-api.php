<?php

namespace MOJ\Intranet\GcoeFeedApi;

defined('ABSPATH') || exit;

require_once 'traits/constants.php';
require_once 'traits/page-content.php';
require_once 'traits/routes.php';
require_once 'traits/user.php';
require_once 'traits/utils.php';

use WP_REST_Request;
use WP_Error;

/**
 * Governance Centre of Expertise Feed API
 *
 * This file contains the API for the Governance Centre of Expertise feed.
 * It is used to export HQ content regarding:
 * - Analysis
 * - Communications
 * - Commercial
 * - Counter fraud
 * - Digital
 * - Finance
 * - Grants
 * - HR
 * - Project delivery
 * - Property
 * - Security
 *
 * It is similar to the Synergy feed API, but is simpler because it is intended to be used by
 * developers, instead of being consumed directly by other systems.
 */
class GcoeFeedApi
{
    use Constants;
    use PageContent;
    use Routes;
    use User;
    use Utils;

    public function __construct()
    {
        // Register the REST API routes.
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }


    /**
     * Get the pages for the Synergy API.
     * 
     * This function retrieves the feed for the Synergy API based on the provided parameters.
     * It returns an array containing the matching pages.
     *
     * @return array|WP_Error The response data containing the feed items.
     */
    public function getFeed(): array|WP_Error
    {
        $data = [
            'timestamp' => date(\DateTime::ATOM),
            'items_count' => 0,
            'items' => [],
        ];

        // Loop over the base URIs.
        foreach (self::BASE_URIS as $base_uri => $base_uri_values) {

            // Get the content type for the current iteration, since the request could be for 'all' content types.
            $content_type = $base_uri_values['content_type_label'];

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
            $data['items'][] = self::formatPagePayload($page, $content_type, $breadcrumbs_from_parent);

            // Get all descendants of the page.
            $descendants = self::getAllDescendants($page->ID);

            // Map over the descendants and format them.
            $descendants_formatted = array_map(function ($descendant) use ($content_type, $breadcrumbs_from_parent) {
                return self::formatPagePayload($descendant, $content_type, $breadcrumbs_from_parent);
            }, $descendants);

            // Add the formatted descendants to the response.
            array_push($data['items'], ...$descendants_formatted);
        }

        global $wpdr;
        $documents_formatted = [];

        // Loop over the pages, and add documents to the items.
        foreach ($data['items'] as &$item) {
            // Get the documents from the content of the page.
            $document_ids = $this->getDocumentsFromContent($item['content']);

            // Add document_ids to a linked documents column
            $item['linked_ids'] = $document_ids;

            $breadcrumbs_from_parent = $item['breadcrumbs'];

            foreach ($document_ids as $document_id) {
                $exists_in_document_formatted = self::arrayFind(
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


                $documents_formatted[] = self::formatPagePayload(
                    $document,
                    $item['content_type'],
                    $breadcrumbs_from_parent
                );
            }
        }

        // Add the documents to the items.
        $data['items'] = array_merge($data['items'], $documents_formatted);

        // Create the breadcrumbs strings for each item.
        $data['items'] = array_map(function ($item) {
            // Create a breadcrumbs string from the breadcrumbs array.
            $item['breadcrumbs_string'] = implode(
                ' > ',
                array_map(fn($b) => $b['title'], $item['breadcrumbs'])
            );

            return $item;
        }, $data['items']);

        // Fort by key content_type, then by intranet_page, then id.
        usort($data['items'], function ($a, $b) {

            // Sort by content_type first.
            $content_type_comparison = strcmp($a['content_type'], $b['content_type']);
            if ($content_type_comparison !== 0) {
                return $content_type_comparison;
            }

            // Breadcrumbs excluding the last item (which is the page/document itself).
            $a_breadcrumbs = $a['breadcrumbs'];
            array_splice($a_breadcrumbs, -1);
            $a_breadcrumbs_string = implode(' > ', array_map(fn($b) => $b['title'], $a_breadcrumbs));

            $b_breadcrumbs = $b['breadcrumbs'];
            array_splice($b_breadcrumbs, -1);
            $b_breadcrumbs_string = implode(' > ', array_map(fn($b) => $b['title'], $b_breadcrumbs));

            // First, compare by breadcrumbs string.
            $breadcrumbs_comparison = strcmp($a_breadcrumbs_string, $b_breadcrumbs_string);
            if ($breadcrumbs_comparison !== 0) {
                return $breadcrumbs_comparison;
            }

            // Next, compare by file_type, alphabetical, except html should be last.
            if ($a['file_type'] === 'html' && $b['file_type'] !== 'html') {
                return 1;
            } elseif ($a['file_type'] !== 'html' && $b['file_type'] === 'html') {
                return -1;
            }

            // Finally, document title.
            return strcmp($a['title'], $b['title']);
        });

        // Count the items in the response.
        $data['items_count'] = count($data['items']);

        return $data;
    }


    /**
     * Get the pages for the GCoE API - CSV response.
     *
     * This function retrieves the feed for the GCoE API and outputs it as a CSV file.
     * It sets the appropriate headers for a CSV download and writes the feed data to the output.
     *
     * @param WP_REST_Request $request The request object containing the parameters.
     * @return string The CSV data as a string.
     */
    public function getFeedCsv(WP_REST_Request $request): string
    {
        $data = $this->getFeed($request);

        try {
            // Set the headers for the CSV download.
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="gcoe_feed.csv"');
            // header('Content-Type: text/plain');

            // Stream the CSV data to the browser/client.
            $out = fopen('php://output', 'w');

            // Write the header row.
            fputcsv($out, [
                self::CSV_HEADERS['id'],
                self::CSV_HEADERS['content_type_label'],
                self::CSV_HEADERS['title'],
                self::CSV_HEADERS['author'],
                self::CSV_HEADERS['version_control'],
                self::CSV_HEADERS['file_type'],
                self::CSV_HEADERS['category'],
                self::CSV_HEADERS['url'],
                self::CSV_HEADERS['published'],
                self::CSV_HEADERS['modified'],
            ]);

            // Write each item in the feed to the CSV.
            foreach ($data['items'] as $item) {
                fputcsv($out, [
                    $item['id'],
                    $item['content_type'],
                    $item['title'],
                    $item['author'],
                    $item['version_control'],
                    $item['file_type'],
                    $item['breadcrumbs_string'],
                    $item['url'],
                    $item['published'],
                    $item['modified'],
                ]);
            }

            // Write each item in the feed to the CSV.
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
    public static function getAllDescendants(int $page_id): array
    {
        $get_pages_args = [
            'child_of' => $page_id,
            'sort_column' => 'menu_order',
            'sort_order' => 'ASC',
            'post_type' => 'page',
            'post_status' => 'publish',
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
     * @return array The formatted page payload.
     */
    public static function formatPagePayload(\WP_Post $page, string $content_type, array $breadcrumbs_from_parent): array
    {
        // Authors - get the authors according to co-authors-plus plugin.
        $authors = get_coauthors($page->ID) ?? [];
        $author_names = array_map(fn($author) => $author->display_name, $authors);

        $file_type = 'html';
        $version_control = 'N';

        if ($page->post_type === 'document') {
            global $wpdr;
            $attach = $wpdr->get_document($page->ID);
            $file = get_attached_file($attach?->ID ?? 0);
            $file_type = pathinfo($file, PATHINFO_EXTENSION);
            $version_control = 'Y';
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
            // Content type, e.g. 'hr', 'finance', 'commercial', 'guidance'.
            'content_type' => $content_type,
            // Menu order may be useful in working out hierarchy or order of pages.
            'menu_order' => $page->menu_order,
            // Less important properties...
            'status' => $page->post_status,
            // Post type
            'type' => $page->post_type,
            // File type, if applicable.
            'file_type' => $file_type,
            // Category, i.e. the patent pages. e.g. HR or HR > Conduct and behaviour > Declarations of interest etc.
            'breadcrumbs' => self::getBreadcrumbs($breadcrumbs_from_parent, $page),
            // Version control flag for documents.
            'version_control' => $version_control,
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

new GcoeFeedApi();
