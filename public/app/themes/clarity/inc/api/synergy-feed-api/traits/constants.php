<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

trait Constants
{
    const CSV_HEADERS = [
        'id' => 'ID',
        'linked_ids' => 'Linked IDs',
        'title' => 'Document Title',
        'agency' => 'Organisation',
        'additional_agencies' => 'Additional Organisations',
        'content_type' => 'Functional Area',
        'category' => 'Category',
        'status' => 'Status',
        'location' => 'Location',
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
        // HR content
        // These URLs were copied by visiting all Agency's Intranets and extracting the HR link from the top menu.
        '/guidance/hr/' => [
            'agency' => 'hq',
            'content_type' => 'hr',
        ],
        '/guidance/human-resources-2/' => [
            'agency' => 'cica',
            'content_type' => 'hr',
        ],
        '/corporate-services/human-resources/' => [
            'agency' => 'jac',
            'content_type' => 'hr',
        ],
        '/guidance/hr-matters/' => [
            'agency' => 'judicial-office',
            'content_type' => 'hr',
        ],
        '/guidance/hr-law-commission/' => [
            'agency' => 'law-commission',
            'content_type' => 'hr',
        ],
        '/hmcts-human-resources/' => [
            'agency' => 'hmcts',
            'content_type' => 'hr',
        ],
        '/guidance/human-resources/' => [
            'agency' => 'laa',
            'content_type' => 'hr',
        ],
        '/guidance/hr-opg/' => [
            'agency' => 'opg',
            'content_type' => 'hr',
        ],
        // Finance content
        // These URLs were copied by visiting all Agency's Intranets clicking 'Guidance & forms' in the top menu,
        // and then identifying the 'Finance' link on that page.
        '/guidance/financial-management/' => [
            'agency' => 'hq',
            'content_type' => 'finance',
        ],
        '/guidance/financial-management-2/' => [
            'agency' => 'cica',
            'content_type' => 'finance',
        ],
        '/corporate-services/finance/' => [
            'agency' => 'jac',
            'content_type' => 'finance',
        ],
        '/guidance/finance/' => [
            'agency' => 'judicial-office',
            'content_type' => 'finance',
        ],
        '/guidance/finance-law-commission/' => [
            'agency' => 'law-commission',
            'content_type' => 'finance',
        ],
        '/guidance/finance-job-cards/' => [
            'agency' => 'law-commission',
            'content_type' => 'finance',
        ],
        '/guidance/finance-and-purchasing/' => [
            'agency' => 'laa',
            'content_type' => 'finance',
        ],
        // Commercial content
        '/guidance/procurement/' => [
            'agency' => 'hq',
            'content_type' => 'commercial',
        ],
        // Guidance, excluding HR, Finance & Commercial
        // These URLs were provided by the Synergy team.
        '/guidance/learning-and-development-2/' => [
            'agency' => 'cica',
            'content_type' => 'guidance',
        ],
        '/guidance/business-travel/' => [
            'agency' => 'cica',
            'content_type' => 'guidance',
        ],
        '/guidance/reward-and-recognition/' => [
            'agency' => 'cica',
            'content_type' => 'guidance',
        ],
        '/guidance/operations-area/' => [
            'agency' => 'cica',
            'content_type' => 'guidance',
        ],
        // 2 pages on JAC's /corporate-services, that are not HR, Finance or Commercial.
        '/corporate-services/fraud-and-whistleblowing/' => [
            'agency' => 'jac',
            'content_type' => 'guidance',
        ],
        '/corporate-services/jac-staff-networks/' => [
            'agency' => 'jac',
            'content_type' => 'guidance',
        ],
        '/guidance/learning-development-in-the-judicial-office/' => [
            'agency' => 'judicial-office',
            'content_type' => 'guidance',
        ],
        '/guidance/learning-and-development-law-commission/' => [
            'agency' => 'law-commission',
            'content_type' => 'guidance',
        ],
        '/guidance/learning-and-development-3/' => [
            'agency' => 'laa',
            'content_type' => 'guidance',
        ],
        '/guidance/contract-management/' => [
            'agency' => 'laa',
            'content_type' => 'guidance',
        ],
    ];

    // Derived from BASE_URIS agency values, prepended with 'all'.
    const AGENCIES = [
        'all',
        'hq',
        'cica',
        'jac',
        'judicial-office',
        'law-commission',
        'hmcts',
        'laa',
        'opg'
    ];

    // Derived from BASE_URIS content_type values, prepended with 'all'.
    const CONTENT_TYPES = [
        'all',
        'hr',
        'finance',
        'commercial',
        'guidance'
    ];
}
