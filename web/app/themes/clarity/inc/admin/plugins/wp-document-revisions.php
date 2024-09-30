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
    public function __construct()
    {
        // load hooks here, inside WP ecosys...
        $this->hooks();
    }

    public function hooks(): void
    {
        // Filter using gzip, always return false, let nginx handle gzipping where necessary.
        add_filter('document_serve_use_gzip', '__return_false', null, 2);
    }
}

new WPDocumentRevisions();