<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

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
    CONST HR_BASE_URIS = [
        // These URLs were copied by visiting all Agency Intranets and extracting the HR link from the top menu.
        '/guidance/hr' => [
            'agencies' => ['hq', 'lawcomm', 'ospt'],
            'content_type' => 'hr',
        ],
        '/guidance/human-resources-2' => [
            'agencies' => ['cica'],
            'content_type' => 'hr',
        ],
        '/corporate-services/human-resources' => [
            'agencies' => ['jac'],
            'content_type' => 'hr',
        ],
        'guidance/hr-matters' => [
            'agencies' => ['jo'],
            'content_type' => 'hr',
        ],
        '/hmcts-human-resources' => [
            'agencies' => ['hmcts'],
            'content_type' => 'hr',
        ],
        '/guidance/human-resources' => [
            'agencies' => ['laa'],
            'content_type' => 'hr',
        ],
        '/guidance/hr-opg' => [
            'agencies' => ['opg'],
            'content_type' => 'hr',
        ]
    ];

    public function __construct()
    {
        // Register the REST API routes.
        add_action('rest_api_init', [$this, 'registerRoutes']);

        // Allow a subset of users to create an application password for themselves.
        add_filter('rest_authentication_errors', [$this, 'allowUserRestRouteForAdmins'], 11);
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
            // URL is /wp-json/synergy/v1/feed
            '/feed',
            [
                'methods'  => 'GET',
                'callback' => [$this, 'getFeed'],
                'permission_callback' => [$this, 'userHasPermission'],
                // When we accept arguments, add args and validation here.

                'args' => [
                    'format' => [
                        'type'    => 'string',
                        'default' => 'markdown',
                        'enum'    => ['html', 'markdown'],
                        'validate_callback' => fn($param) => in_array($param, ['html', 'markdown'])
                    ],
                ],
            ]
        );
    }

    public function getFeed(\WP_REST_Request $request): \WP_REST_Response
    {
        $format = $request->get_param('format');

        // Get all pages that have a URI starting with /guidance/hr

        
        // $agency = 'hq';
        $root_uri = '/guidance/hr';
        $root_uri = '/hmcts-human-resources';
        
        // Get the page with the root URI.
        $page = get_page_by_path($root_uri, OBJECT, 'page');
        
        if (!$page) {
            return new \WP_REST_Response(['error' => 'Page not found'], 404);
        }

        $descendants = $this->getAllDescendants($page->ID);

        $descendants_formatted = array_map(function ($descendant) use ($format) {
            return $this->formatPagePayload($descendant, $format);
        }, $descendants);

        return new \WP_REST_Response($descendants_formatted, 200);


        // error_log( print_r($descendants, ) . ' descendants for page ID ' . $page->ID);

        $response = [$this->formatPagePayload($page, $format)];

        return new \WP_REST_Response($response, 200);
    }

    public function getAllDescendants($page_id)
    {
        $descendants = get_pages([
            'child_of' => $page_id,
            'sort_column' => 'menu_order',
            'sort_order' => 'ASC',
            'post_type' => 'page',
        ]);

        return $descendants;
    }


    public function formatPagePayload($page, $format = 'html')
    {
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

        $page = array_intersect_key((array) $page, array_flip($keep_properties));

        $page['permalink'] = get_permalink($page['ID']);

        if ('html' === $format) {
            $page['post_content_html'] = apply_filters('the_content', $page['post_content']);
            unset($page['post_content']);
        }

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
