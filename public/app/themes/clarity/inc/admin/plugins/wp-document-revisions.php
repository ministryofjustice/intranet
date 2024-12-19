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
    private $wp_document_revisions = null;

    public function __construct()
    {
        // Set class properties.
        $this->site_url = get_site_url();
        $this->home_url = get_home_url();

        // load hooks here, inside WP ecosys...
        $this->hooks();
    }

    public function hooks(): void
    {
        add_filter('document_permalink', [$this, 'filterPermalink'], 10, 2);
        // Filter using gzip, always return false, let nginx handle gzipping where necessary.
        add_filter('document_serve_use_gzip', '__return_false', null, 2);
        // Filter to retry missing files.
        add_filter('get_attached_file', [$this, 'retryFilesNotFound'], 15, 2);
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
}
