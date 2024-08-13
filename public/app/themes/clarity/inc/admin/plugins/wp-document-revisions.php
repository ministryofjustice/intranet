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
}
