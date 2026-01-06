<?php

/**
 * Modifications to adapt the wp-document-revisions plugin.
 *
 * @package Clarity
 **/

namespace MOJ\Intranet;

use WP_Error;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Actions and filters related to WordPress documents post type.
 */
class WPDocumentRevisions
{

    private $home_url = '';
    private $site_url = '';
    private $document_url_regex = '';
    private $wp_document_revisions = null;

    public function __construct()
    {
        // Set class properties.
        $this->site_url = get_site_url();
        $this->home_url = get_home_url();

        // Set the document URL regex - optional home_url, followed by /documents/.
        $this->document_url_regex = '/^(' . preg_quote($this->home_url, '/') . ')?\/documents/';

        // Add document_revisions to the cache's non-persistent groups.
        // Because, the values for this group are different for the list and single views.
        wp_cache_add_non_persistent_groups('document_revisions');

        // load hooks here, inside WP ecosys...
        $this->hooks();
    }

    public function hooks(): void
    {
        add_filter('document_permalink', [$this, 'filterPermalink'], 10, 2);
        // Filter to remove trailing slash from the document's URL.
        add_filter('user_trailingslashit', [$this, 'filterTrailingSlash'], 10, 2);
        // Filter using gzip, always return false, let nginx handle gzipping where necessary.
        add_filter('document_serve_use_gzip', '__return_false', null, 2);
        // Filter to retry missing files.
        add_filter('get_attached_file', [$this, 'retryFilesNotFound'], 15, 2);
        // Filter the document get_revisions result to correct the author.
        add_filter('wp_document_revisions_get_revisions', [$this, 'filterGetMostRecentRevision'], 10, 2);
        // Filter the get_latest_revision result to correct the author.
        add_filter('wp_document_revisions_get_latest_revision', [$this, 'filterGetLatestRevision'], 10, 2);
        // Filter wp_die handler for documents - to change 403 to 404 for missing document files.
        add_filter('wp_die_handler', [$this, 'filterWpDieHandler']);
    }


    /**
     * Update the document's permalink, specifically preview links that are not correctly structured.
     * 
     * @param string $link The permalink.
     * @param null|object|array $document The document.
     * 
     * @return string The filtered permalink.
     */
    public function filterPermalink(string $link, null|object|array $document)
    {
        // Do nothing if the document is published.
        if (get_post_status($document) === 'publish') {
            return $link;
        }

        // Remove unnecessary `/wp` from the link.
        return str_replace($this->site_url, $this->home_url, $link);
    }

    /**
     * Remove trailing slash from the document's URL.
     *
     * This is required because the site-wide permalink structure is set to include a trailing slash.
     * Here, that default is overridden to remove the trailing slash for the document post type.
     * 
     * This affects the URL in various places, e.g.
     * - the document's URL on the document edit screen.
     * - the URL for document downloads.
     * - document URLs accessed with a trailing slash will be redirected to remove it.
     * 
     * @param string $string The URL.
     * @return string The filtered URL.
     */
    public function filterTrailingSlash($string)
    {
        if (preg_match($this->document_url_regex, $string)) {
            return untrailingslashit($string);
        }

        return $string;
    }

    /**
     * Extract the date from the file path.
     * 
     * @param string $file The file path.
     * @return string|null The date.
     */
    private function getDateFromFile(string $file): ?string
    {
        $matches = [];
        preg_match('/\/media\/(\d{4}\/\d{2})\//', $file, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Extract the date from the URL (guid).
     * 
     * @param string $url The URL.
     * @return string|null The date.
     */
    private function getDateFromUrl(string $url): ?string
    {
        $matches = [];
        preg_match('/\/documents\/(\d{4}\/\d{2})\//', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Retry missing document/file, with date from attachment guid.
     * 
     * This function has been added because files are not being served correctly 
     * when the published date has been updated. In the function, we check if the 
     * file exists, if it doesn't the we extract the date from the attachment's 
     * guid and replace the date in the file path.
     * 
     * @param string $file The file path.
     * @param int $attachment_id The attachment ID.
     * @return string The file path.
     */
    public function retryFilesNotFound($file, $attachment_id)
    {
        if (!$file) {
            return $file;
        }

        if (!$this->wp_document_revisions) {
            // Make sure we are dealing with a document.
            $this->wp_document_revisions = new \WP_Document_Revisions();
        }

        if (!$this->wp_document_revisions->verify_post_type($attachment_id)) {
            return $file;
        }

        if (is_file($file)) {
            return $file;
        }

        $attachment = get_post($attachment_id);

        if (!$attachment) {
            return $file;
        }

        $dates = [
            'file' => $this->getDateFromFile($file),
            'guid' => $this->getDateFromUrl($attachment->guid),
        ];

        if ($dates['file'] === $dates['guid'] || $dates['file'] === null || $dates['guid'] === null) {
            return $file;
        }

        // Let's replace the date with the one from the attachment's guid.
        $new_file = str_replace($dates['file'], $dates['guid'], $file);

        // If the new file exists, return it.
        if (is_file($new_file)) {
            return $new_file;
        }

        // If the file still doesn't exist, return the original file.
        return $file;
    }

    /**
     * Get the most recent revision author.
     * 
     * @param string $format The format to return the author in.
     * @param int|null $post_id The post ID.
     * @return int|string The author ID or display name.
     */
    public function getMostRecentRevisionAuthor(string $format = 'id', int|null $post_id = null): int|string
    {
        // Only allow 'id' or 'displayname' as formats.
        if (!in_array($format, ['id', 'displayname'])) {
            return 0;
        }

        // If we don't have a post ID, but we are in the loop, get the post ID.
        $post_id = $post_id ?: get_the_ID();

        // If we still don't have a post ID, return 0.
        if (!$post_id) {
            return 0;
        }

        // If we haven't already set the wp_document_revisions object, do so now.
        if (!$this->wp_document_revisions) {
            $this->wp_document_revisions = new \WP_Document_Revisions();
        }

        // Get the revisions for the current post - the first in the array is the document, technically not a revision.
        $document_revisions = $this->wp_document_revisions->get_revisions($post_id);

        // In an edge case we might not have any revisions. If so, return 0.
        if (empty($document_revisions) || !is_array($document_revisions)) {
            return 0;
        }

        // Get the most recent revision, if there are no revisions, use the original document.
        $most_recent = $document_revisions[1] ?? $document_revisions[0];

        // If we can't find the author, return the 0;
        if (!$most_recent?->post_author || !is_numeric($most_recent->post_author)) {
            return 0;
        }

        // Return the author ID or display name.
        if ($format === 'id') {
            return (int) $most_recent->post_author;
        }

        return get_user_by('ID', $most_recent->post_author)?->display_name ?? 0;
    }

    /**
     * Filter the get_revisions result to correct the author.
     *
     * When this filter is called in the context of 'revision_metabox',
     * it is used to correct the author on the first row of the revisions table.
     *
     * @param false|array $revisions The revisions.
     * @param string $context The context.
     * @return false|array The filtered revisions.
     */
    public function filterGetMostRecentRevision($revisions, string $context)
    {
        if ('revision_metabox' !== $context) {
            return $revisions;
        }

        if (empty($revisions) || !is_array($revisions) || !isset($revisions[1]?->post_author)) {
            return $revisions;
        }

        $revisions[0]->post_author = $revisions[1]->post_author;

        return $revisions;
    }

    /**
     * Filter the get_latest_revision result to correct the author.
     * 
     * When this filter is called in the context of 'document_metabox',
     * it is used to correct the author in the string 'Checked in x ago by y'.
     * 
     * @param false|WP_Post $revision The revision.
     * @param string $context The context.
     * @return false|WP_Post The filtered revision.
     */
    public function filterGetLatestRevision($revision, string $context)
    {
        if ('document_metabox' !== $context) {
            return $revision;
        }

        if (empty($revision) || !is_object($revision)) {
            return $revision;
        }

        $revision->post_author = $this->getMostRecentRevisionAuthor('id');

        return $revision;
    }


    /**
     * Filter the wp_die handler to use a custom wrapper for documents.
     *
     * The reason for the custom wrapper is that the WP Document Revisions plugin
     * calls wp_die with a 403 response code when a document file is missing.
     * We want to change the response code to 404, but there is no filter in the plugin to do this directly.
     * So we wrap the wp_die handler and modify the response code when necessary.
     *
     * @param callable $handler The original wp_die handler.
     * @return callable The filtered wp_die handler.
     */
    public function filterWpDieHandler(callable $handler): callable
    {
        global $post;

        // If we are dealing with a document post type, and wpDieWrapper has not already been applied.
        if ($post?->post_type === 'document' && empty($post->wpdr_die_wrapper_applied)) {
            return [$this, 'wpDieWrapper'];
        }

        // Otherwise, return the original handler.
        return $handler;
    }


    /**
     * Custom wp_die handler wrapper for documents.
     *
     * When a document is missing a file, we want to change the response code from 403 to 404.
     * This wrapper checks for that specific case and modifies the response code accordingly.
     *
     * This can be tested by creating a document, and not uploading a file to it.
     * Then publish the document and click the preview link, the resulting error should be a 404, not a 403.
     *
     * @param string|WP_Error $message The message to display.
     * @param string $title The title of the error.
     * @param string|array $args Additional arguments.
     * @return void
     */
    public function wpDieWrapper($message, $title, string|array $args = []): void
    {
        global $post;

        // Create a `wpdr_die_wrapper_applied` property on the global $post object.
        // In `filterWpDieHandler`, this property is checked to avoid re-wrapping the wp_die handler.
        // This is essential to prevent an infinite loop.
        $post->wpdr_die_wrapper_applied = true;

        // There is a specific case where we want to change the response code from 403 to 404.
        // This is when the message is 'No document file is attached.' and the response code is 403.
        // See: `wp-document-revisions/includes/class-wp-document-revisions.php`
        $target_message = esc_html__('No document file is attached.', 'wp-document-revisions');
        if ($message === $target_message && is_array($args) && $args['response'] === 403) {
            $args['response'] = 404;
        }

        // Finally re-call wp_die function with the message and (possibly) modified args.
        wp_die($message, $title, $args);
    }
}
