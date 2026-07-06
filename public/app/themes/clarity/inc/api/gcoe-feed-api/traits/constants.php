<?php

namespace MOJ\Intranet\GcoeFeedApi;

defined('ABSPATH') || exit;

trait Constants
{
    const CSV_HEADERS = [
        'id' => 'ID',
        'linked_ids' => 'Linked IDs',
        'title' => 'Document Title',
        'content_type_label' => 'Function',
        'category' => 'Breadcrumbs',
        'file_type' => 'Document format',
        'url' => 'Link',
        'author' => 'Author',
        'published' => 'Published Date',
        'modified' => 'Last Modified Date',
        'version_control' => 'Version Control',
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
            'content_type_label' => 'Analysis',
        ],
        '/guidance/communications/' => [
            'content_type' => 'communications',
            'content_type_label' => 'Communications',
        ],
        '/guidance/procurement/' => [
            'content_type' => 'commercial',
            'content_type_label' => 'Commercial',
        ],
        '/guidance/security/report-a-security-incident/fraud/' => [
            'content_type' => 'counter-fraud',
            'content_type_label' => 'Counter fraud',
        ],
        '/guidance/it-services/' => [
            'content_type' => 'digital',
            'content_type_label' => 'Digital',
        ],
        '/guidance/financial-management/' => [
            'content_type' => 'finance',
            'content_type_label' => 'Finance',
        ],
        '/guidance/financial-management/grants/' => [
            'content_type' => 'grants',
            'content_type_label' => 'Grants',
        ],
        '/guidance/hr/' => [
            'content_type' => 'hr',
            'content_type_label' => 'HR',
        ],
        '/guidance/project-delivery/' => [
            'content_type' => 'project-delivery',
            'content_type_label' => 'Project delivery',
        ],
        '/guidance/buildings-and-facilities/' => [
            'content_type' => 'property',
            'content_type_label' => 'Property',
        ],
        '/guidance/security/' => [
            'content_type' => 'security',
            'content_type_label' => 'Security',
        ],
    ];
}
