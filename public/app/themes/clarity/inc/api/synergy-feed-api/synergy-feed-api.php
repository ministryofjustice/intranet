<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

require_once 'traits/page-content.php';
require_once 'traits/utils.php';

/**
 * Synergy Feed API
 *
 * This file contains the API for the Synergy feed.
 * It is used to serve content regarding:
 * - HR
 * - Finance & Commercial policy
 * - Guidance information
 */
class SynergyFeedApi
{
    use PageContent;
    use Utils;

    const BASE_URIS = [
        // HR content
        // These URLs were copied by visiting all Agency's Intranets and extracting the HR link from the top menu.
        'guidance/hr' => [
            'agencies' => ['hq', 'lawcomm', 'ospt'],
            'content_type' => 'hr',
        ],
        'guidance/human-resources-2' => [
            'agencies' => ['cica'],
            'content_type' => 'hr',
        ],
        'corporate-services/human-resources' => [
            'agencies' => ['jac'],
            'content_type' => 'hr',
        ],
        'guidance/hr-matters' => [
            'agencies' => ['jo'],
            'content_type' => 'hr',
        ],
        'hmcts-human-resources' => [
            'agencies' => ['hmcts'],
            'content_type' => 'hr',
        ],
        'guidance/human-resources' => [
            'agencies' => ['laa'],
            'content_type' => 'hr',
        ],
        'guidance/hr-opg' => [
            'agencies' => ['opg'],
            'content_type' => 'hr',
        ]
    ];

    public $agencies = [];

    public $content_types = [];

    public function __construct()
    {
        // Initialise the agencies and content types properties.
        $this->initProperties();

        // Register the REST API routes.
        add_action('rest_api_init', [$this, 'registerRoutes']);

        // Allow a subset of users to create an application password for themselves.
        add_filter('rest_authentication_errors', [$this, 'allowUserRestRouteForAdmins'], 11);
    }

    /**
     * Initialise the properties of the SynergyFeedApi class.
     * 
     * This method populates the `agencies` and `content_types` properties with the values from the `BASE_URIS` constant.
     * 
     * @return void
     */
    public function initProperties(): void
    {
        foreach ($this::BASE_URIS as $uri => $data) {
            // Add the agencies to the enum for the 'agency' parameter in the REST API route.
            foreach ($data['agencies'] as $agency) {
                if (!in_array($agency, $this->agencies)) {
                    $this->agencies[] = $agency;
                }
            }

            // Add the content type to the enum for the 'content_type' parameter in the REST API route.
            if (!in_array($data['content_type'], $this->content_types)) {
                $this->content_types[] = $data['content_type'];
            }
        }
    }

    public function getBaseUriFromProperties($agency, $content_type): string|null
    {
        // Find the base URI that matches the agency and content type.
        $filtered_uris = array_filter(
            $this::BASE_URIS,
            function ($base_uri) use ($agency, $content_type) {
                return in_array($agency, $base_uri['agencies']) && $content_type === $base_uri['content_type'];
            }
        );

        // If no matching base URI is found, return an empty string.
        if (empty($filtered_uris)) {
            return null;
        }

        // Return the first matching base URI.
        return array_key_first($filtered_uris);
    }

    /**
     * Does the current user have permission to: 
     * - access the Synergy feed, and
     * - create an application password
     * 
     * @return bool True if the user has permissions, false otherwise.
     */
    public function userHasPermission(): bool
    {
        return current_user_can('administrator') || current_user_can('synergy_feed');
    }

    /**
     * Register the REST API routes for the Synergy feed.
     *
     * @return void
     */
    public function registerRoutes(): void
    {
        register_rest_route(
            'synergy/v1',
            'feeds',
            [
                'methods'  => 'GET',
                'callback' => fn() => $this::BASE_URIS,
                'permission_callback' => [$this, 'userHasPermission'],
            ]
        );

        register_rest_route(
            'synergy/v1',
            // URL is /wp-json/synergy/v1/feed
            '/feed',
            [
                'methods'  => 'GET',
                'callback' => [$this, 'getFeed'],
                'permission_callback' => [$this, 'userHasPermission'],
                'validate_callback' => function ($request) {
                    // Ensure the request is valid - look for an entry in BASE_URIS with the requested agency and content_type parameters.
                    $base_uri = $this->getBaseUriFromProperties(
                        $request->get_param('agency'),
                        $request->get_param('content_type')
                    );

                    // If no base URI is found, return a WP_Error with a 400 status code.
                    if(!$base_uri) {
                        // If no base URI is found, return a WP_Error with a 400 status code.
                        return new \WP_Error(
                            'invalid_agency_or_content_type',
                            'Invalid agency and content type combination provided.',
                            ['status' => 400]
                        );
                    }

                    // If we have a base URI, then the request is valid.
                    return true;
                },
                'args' => [
                    'agency' => [
                        'type'    => 'string',
                        'default' => 'hq',
                        'enum' => $this->agencies,
                    ],
                    'content_type' => [
                        'type'    => 'string',
                        'default' => 'hr',
                        'enum' => $this->content_types,
                    ],
                    'modified_after' => [
                        'type'    => 'string',
                        'required' => false,
                        'default' => '',
                        'validate_callback' => function ($param) {
                            // Ensure it's a date or datetime in ISO format

                            if (empty($param)) {
                                return true; // If no value is provided, it's valid.
                            }

                            // Example value: modified_after=2024-05-01T00:00:00
                            // Example value: modified_after=2024-05-01T00:00:00Z
                            // Example value: modified_after=2024-05-01T08:03:00%2b01:00
                            // Example value: modified_after=2024-05-01T08:03:00-01:00

                            $iso_pattern = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[\+\-]\d{2}:\d{2})?$/';

                            return (bool) preg_match($iso_pattern, $param);
                        },
                    ],
                    'format' => [
                        'type'    => 'string',
                        'default' => 'markdown',
                        'enum'    => ['html', 'markdown'],
                        // validate_callback is not needed here as 'enum' already validates the value.
                    ],
                ],
            ]
        );
    }

    public function getFeed(\WP_REST_Request $request): \WP_REST_Response
    {
        $format = $request->get_param('format');

        $modified_after = $request->get_param('modified_after');

        $agency = $request->get_param('agency');

        $content_type = $request->get_param('content_type');

        $base_uri = $this->getBaseUriFromProperties($agency, $content_type);

        $response = [
            'format' => $format,
            'modified_after' => $modified_after,
            'agency' => $agency,
            'content_type' => $content_type,
            'base_uri' => $base_uri,
            'items' => [],
        ];

        // Get the page with the root URI.
        $page = get_page_by_path($base_uri, OBJECT, 'page');

        if (!$page) {
            return new \WP_REST_Response(['error' => 'Page not found'], 404);
        }

        $root_page_formatted = $this->formatPagePayload($page, $format);

        $descendants = $this->getAllDescendants(page_id: $page->ID, modified_after: $modified_after);

        $descendants_formatted = array_map(function ($descendant) use ($format) {
            return $this->formatPagePayload($descendant, $format);
        }, $descendants);

        $response['items'] = array_merge([$root_page_formatted], $descendants_formatted);

        return new \WP_REST_Response($response, 200);
    }

    /**
     * Modify the query arguments for get_pages to include a date_query.
     * 
     * This is necessary because get_pages does not support date_query by default.
     * 
     * @param array $query_args The query arguments for get_pages.
     * @param array $passed_args The arguments passed to the get_pages function.
     * @return array The modified query arguments.
     */
    public function modifyGetPagesQueryArgs($query_args, $passed_args)
    {
        $query_args['date_query'] = $passed_args['date_query'] ?? [];

        return $query_args;
    }

    /**
     * Get all descendants of a page, optionally filtered by modified date.
     * 
     * @param int $page_id The ID of the page to get descendants for.
     * @param string|null  $modified_after Optional. A date in ISO 8601 format to filter descendants by their last modified date.
     * @return array An array of page objects representing the descendants.
     */
    public function getAllDescendants(int $page_id, string|null $modified_after): array
    {
        $get_pages_args = [
            'child_of' => $page_id,
            'sort_column' => 'menu_order',
            'sort_order' => 'ASC',
            'post_type' => 'page',
            // Optionally add a date query to filter by modified date.
            ...(!empty($modified_after) ? ['date_query' => [
                'column' => 'post_modified',
                'after' => $modified_after,
            ]] : []),
        ];

        // Add a filter to modify the query args for get_pages.
        // This is necessary to ensure that the date_query is applied correctly.
        add_filter('get_pages_query_args', [$this, 'modifyGetPagesQueryArgs'], 10, 2);

        $descendants = get_pages($get_pages_args);

        // Remove the filter after we have retrieved the pages.
        // This is important to avoid affecting other queries that use get_pages.
        remove_filter('get_pages_query_args', [$this, 'modifyGetPagesQueryArgs'], 10, 2);


        return $descendants;
    }


    public function formatPagePayload($page, $format = 'html')
    {
        $page->post_content = $this->getPageContent($page, $format);

        // Filter properties from $page
        $keep_properties = [
            'ID',
            'post_title',
            'post_content',
            'post_excerpt',
            'post_date',
            'post_modified',
            'post_type',
            'post_parent',
            'post_status',
        ];

        return $page;

        // TODO
        $page = array_intersect_key((array) $page, array_flip($keep_properties));



        $page['permalink'] = get_permalink($page['ID']);


        // Format post_date and post_modified to ISO 8601 format.
        $page['post_date'] = date(\DateTime::ATOM, strtotime($page['post_date']));
        $page['post_modified'] = date(\DateTime::ATOM, strtotime($page['post_modified']));

        // Agencies - this is equivalent to the 'Content tagged as' part of the rendered page.
        $agencies         = get_the_terms($page['ID'], 'agency');
        $page['agencies'] = is_array($agencies) ? array_map(fn($agency) => $agency->name, $agencies) : null;

        // Authors - get the authors according to co-authors-plus plugin.
        $authors = get_coauthors($page['ID']) ?? [];
        $page['authors'] = array_map(fn($author) => [
            'ID' => $author->ID,
            'display_name' => $author->display_name,
            'user_nicename' => $author->user_nicename,
        ], $authors);

        return $page;
    }

    /**
     * This function allows the user to access the /wp/v2/users/me REST route
     * if they are an administrator. If not, it returns the result of the
     * rest_authentication_errors filter.
     * 
     * This is a thorough workaround for the security plugin that blocks access to the
     * /wp/v2/users/<own_user_id>/application-passwords REST route for all users.
     * 
     * @param WP_Error|null The result of the rest_authentication_errors filter, so far.
     * @return WP_Error|null The filtered result, null if conditions are met.
     */
    public function allowUserRestRouteForAdmins($result)
    {
        // Check if class exists, if not then do noting.
        if (!class_exists('MOJComponents\Security\FilterRestAPI')) {
            return $result;
        }

        // Check if the passed in value is an error, if not then do nothing.
        if (!is_wp_error($result)) {
            return $result;
        }

        // Check if we are an administrator, if not then do nothing.
        if (!current_user_can('administrator')) {
            return $result;
        }

        // Check if we are on the specific REST API route, if not then do nothing.
        $rest_route = $GLOBALS['wp']->query_vars['rest_route'];

        if (!$rest_route) {
            return $result;
        }

        // Current user ID
        $current_user_id = get_current_user_id();

        $allowed_rest_route = '/wp/v2/users/' . $current_user_id . '/application-passwords';

        if ($rest_route !== $allowed_rest_route && !str_starts_with($rest_route, $allowed_rest_route . '/')) {
            return $result;
        }

        // Check if the referrer is the allowed referrer, if not then do nothing.
        $allowed_referrer = get_admin_url(null, 'profile.php');

        if (wp_get_referer() !== $allowed_referrer) {
            return $result;
        }

        // Check if the error message is the one we are looking for, if not then do nothing.
        $error_messages = $result->get_error_messages();
        if (count($error_messages) !== 1 || $error_messages[0] !== esc_html__('Only authenticated users can access the REST API.')) {
            return $result;
        }

        // Check if the error data is the one we are looking for, if not then do nothing.
        $error_data = $result->get_error_data();
        if (count($error_data) !== 1 || $error_data['status'] !== 403) {
            return $result;
        }

        // If we are here, the whe have satisfied the following conditions:
        // 1. The class exists.
        // 2. The passed in value is an error(s).
        // 3. The user is an administrator.
        // 4. The REST API route is one of the allowed routes.
        // 5. The referrer is the allowed referrer.
        // 6. The error message is the one we are looking for.
        // 7. The error data is the one we are looking for.
        // So we can return null to allow the admin user to access the REST API route.

        return null;
    }
}

new SynergyFeedApi();
