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
            'agencies' => ['hq', 'ospt'],
            'content_type' => 'hr',
        ],
        '/guidance/human-resources-2/' => [
            'agencies' => ['cica'],
            'content_type' => 'hr',
        ],
        '/corporate-services/human-resources/' => [
            'agencies' => ['jac'],
            'content_type' => 'hr',
        ],
        '/guidance/hr-matters/' => [
            'agencies' => ['jo'],
            'content_type' => 'hr',
        ],
        '/guidance/hr-law-commission/' => [
            'agencies' => ['law-commission'],
            'content_type' => 'hr',
        ],
        '/hmcts-human-resources/' => [
            'agencies' => ['hmcts'],
            'content_type' => 'hr',
        ],
        '/guidance/human-resources/' => [
            'agencies' => ['laa'],
            'content_type' => 'hr',
        ],
        '/guidance/hr-opg/' => [
            'agencies' => ['opg'],
            'content_type' => 'hr',
        ],
        // Finance content
        // These URLs were copied by visiting all Agency's Intranets clicking 'Guidance & forms' in the top menu,
        // and then identifying the 'Finance' link on that page.
        '/guidance/financial-management/' => [
            'agencies' => ['hq'],
            'content_type' => 'finance',
        ],
        '/guidance/financial-management-2/' => [
            'agencies' => ['cica'],
            'content_type' => 'finance',
        ],
        '/corporate-services/finance/' => [
            'agencies' => ['jac'],
            'content_type' => 'finance',
        ],
        '/guidance/finance/' => [
            'agencies' => ['jo'],
            'content_type' => 'finance',
        ],
        '/guidance/finance-law-commission/' => [
            'agencies' => ['law-commission'],
            'content_type' => 'finance',
        ],
        '/guidance/finance-job-cards/' => [
            'agencies' => ['law-commission'],
            'content_type' => 'finance',
        ],
        '/guidance/finance-and-purchasing/' => [
            'agencies' => ['laa'],
            'content_type' => 'finance',
        ],
        // Commercial content
        '/guidance/procurement/' => [
            'agencies' => ['hq'],
            'content_type' => 'commercial',
        ],
        // Guidance, excluding HR, Finance & Commercial
        // These URLs were provided by the Synergy team.
        '/guidance/learning-and-development-2/' => [
            'agencies' => ['cica'],
            'content_type' => 'guidance',
        ],
        '/guidance/business-travel/' => [
            'agencies' => ['cica'],
            'content_type' => 'guidance',
        ],
        '/guidance/reward-and-recognition/' => [
            'agencies' => ['cica'],
            'content_type' => 'guidance',
        ],
        '/guidance/operations-area/' => [
            'agencies' => ['cica'],
            'content_type' => 'guidance',
        ],
        // 2 pages on JAC's /corporate-services, that are not HR, Finance or Commercial.
        '/corporate-services/fraud-and-whistleblowing/' => [
            'agencies' => ['jac'],
            'content_type' => 'guidance',
        ],
        '/corporate-services/jac-staff-networks/' => [
            'agencies' => ['jac'],
            'content_type' => 'guidance',
        ],
        '/guidance/learning-development-in-the-judicial-office/' => [
            'agencies' => ['jo'],
            'content_type' => 'guidance',
        ],
        '/guidance/learning-and-development-law-commission/' => [
            'agencies' => ['law-commission'],
            'content_type' => 'guidance',
        ],
        '/guidance/learning-and-development-3/' => [
            'agencies' => ['laa'],
            'content_type' => 'guidance',
        ],
        '/guidance/contract-management/' => [
            'agencies' => ['laa'],
            'content_type' => 'guidance',
        ],
    ];
}
