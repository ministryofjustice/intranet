<?php

namespace MOJ\Intranet\GcoeFeedApi;

defined('ABSPATH') || exit;

trait Constants
{
    const CSV_HEADERS = [
        'id' => 'ID',
        'linked_ids' => 'Linked IDs',
        'title' => 'Document Title',
        'content_type' => 'Functional Area',
        'category' => 'Category',
        'status' => 'Status',
        'file_type' => 'File Format',
        'url' => 'Link',
        'author' => 'Author',
        'additional_authors' => 'Additional Authors',
        'published' => 'Published Date',
        'modified' => 'Last Modified Date',
    ];

    const CSV_STATUSES = [
        'draft' => 'Draft',
        'publish' => 'Published',
        'private' => 'Private',
        'future' => 'Future',
        'pending' => 'Pending Review',
    ];

    const BASE_URIS = [
        // These URLs were provided by GCoE,a nd are all HQ.
        '/guidance/research-and-analysis/' => [
            'content_type' => 'analysis',
        ],
        '/guidance/communications/' => [
            'content_type' => 'communications',
        ],
        '/guidance/procurement/' => [
            'content_type' => 'commercial',
        ],
        '/guidance/security/report-a-security-incident/fraud/' => [
            'content_type' => 'counter-fraud',
        ],
        '/guidance/it-services/' => [
            'content_type' => 'digital',
        ],
        '/guidance/financial-management/' => [
            'content_type' => 'finance',
        ],
        '/guidance/financial-management/grants/' => [
            'content_type' => 'grants',
        ],
        '/guidance/hr/' => [
            'content_type' => 'hr',
        ],
        '/guidance/project-delivery/' => [
            'content_type' => 'project-delivery',
        ],
        '/guidance/buildings-and-facilities/' => [
            'content_type' => 'property',
        ],
        '/guidance/security/' => [
            'content_type' => 'security',
        ],
    ];
}
