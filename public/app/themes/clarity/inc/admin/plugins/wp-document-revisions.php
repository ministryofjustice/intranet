<?php

/**
 * Modifications to adapt the wp-document-revisions plugin.
 *
 * @package Clarity
 **/

namespace MOJ\Intranet;

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
        add_filter('wp_document_revisions_get_revisions', [$this, 'filterGetMostRecentRevisionAuthor'], 10, 2);
        // Filter the get_latest_revision result to correct the author.
        add_filter('wp_document_revisions_get_latest_revision', [$this, 'filterGetLatestRevision'], 10, 2);
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
        if (!$post_id && get_the_ID()) {
            $post_id = get_the_ID();
        }

        // If we haven't already set the wp_document_revisions object, do so now.
        if (!$this->wp_document_revisions) {
            $this->wp_document_revisions = new \WP_Document_Revisions();
        }

        // Get the revisions for the current post - the first in the array is the document, technically not a revision.
        $document_revisions = $this->wp_document_revisions->get_revisions(get_the_ID());

        // In an edge case we might not have any revisions. If so, return 0.
        if (empty($document_revisions)) {
            return 0;
        }

        // Get the author of the most recent revision.
        $most_recent_revision_author = $document_revisions[1]->post_author ?? null;

        // If we can't find the author, return the 0;
        if (!$most_recent_revision_author || !is_numeric($most_recent_revision_author)) {
            return 0;
        }

        // Return the author ID or display name.
        if ($format === 'id') {
            return (int) $most_recent_revision_author;
        }

        return get_user_by('ID', $most_recent_revision_author)?->display_name ?? 0;
    }

    /**
     * Filter the get_revisions result to correct the author.
     * 
     * @param array $revisions The revisions.
     * @param string $context The context.
     * @return array The filtered revisions.
     */
    public function filterGetMostRecentRevisionAuthor($revisions, $context)
    {
        if ('revision_metabox' !== $context) {
            return $revisions;
        }

        if (empty($revisions) || !is_array($revisions) || !isset($revisions[1])) {
            return $revisions;
        }

        $revisions[0]->post_author = $revisions[1]->post_author;

        return $revisions;
    }

    /**
     * Filter the get_latest_revision result to correct the author.
     * 
     * @param mixed $revision The revision.
     * @param string $context The context.
     * @return mixed The filtered revision.
     */
    public function filterGetLatestRevision(mixed $revision, string $context): mixed
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
}
