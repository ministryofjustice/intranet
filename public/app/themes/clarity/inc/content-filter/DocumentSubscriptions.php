<?php

namespace MOJ\Intranet;

use WP_Error;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;

class DocumentSubscriptions
{
    /**
     * Context objects to be used in front-end script
     * @var array
     */
    private array $context_objects = [];

    /**
     * Context IDs to prevent duplicates
     * @var array
     */
    private array $context_ids = [];

    /**
     * Loop protection to prevent infinite loops
     * @var int
     */
    private int $loop_protect = 0;

    /**
     * Allowed email domains for subscriptions
     * @var array
     */
    private array $allowed_email_domains = [
        'justice.gov.uk',
        'digital.justice.gov.uk',
        // Add more allowed domains as needed
    ];

    /**
     * DocumentSubscriptions constructor.
     */
    public function __construct()
    {
        $this->hooks();
    }

    /**
     * Register hooks
     */
    public function hooks(): void
    {
        add_filter('acf_the_content', [$this, 'append_option_links'], 100, 1);
        add_filter('the_content', [$this, 'append_option_links'], 100, 1);

        // API
        add_action('rest_api_init', [$this, 'register_rest_route']);
    }

    /**
     * Append option links to the content
     *
     * @param string $content
     *
     * @return string
     */
    public function append_option_links(string $content): string
    {
        // scan the content and append an icon to all links that contain the word "document"
        $pattern = '/<a[^>]+href="([^"]+)"[^>]*>(.*?)<\/a>/i';

        // Use preg_replace_callback to find all links in the content
        $content = preg_replace_callback($pattern, fn($matches) => $this->apply_icons($matches), $content);

        // define a script that describes the context objects
        $context_script = '<script>window.document_option_links = ' . json_encode($this->context_objects) . ';</script>';

        // Append the script to the end of the content
        return $content . $context_script;
    }

    /**
     * Set the context object for the document
     *
     * @param string $context_id
     * @param string $url
     * @param string $name
     */
    public function set_context_object(string $context_id, string $url, string $name): void
    {
        $this->context_objects[] = (object) [
            'id' => $this->get_document_id($name),
            'url' => $url,
            'text' => ucfirst($name),
            'context' => $context_id
        ];
    }

    /**
     * Get a unique context ID for the document
     *
     * @return string
     */
    public function get_context_id_unique(): string
    {
        do {
            $context_id = 'doc_opt_' . uniqid();

            if ($this->loop_protect > 10) {
                $context_id .= '_' . $this->loop_protect;
                break;
            }
            $this->loop_protect++;
        } while (in_array($context_id, $this->context_ids));

        $this->context_ids[] = $context_id;
        return $context_id;
    }

    /**
     * Get the document ID from the title
     *
     * @param string $title
     *
     * @return int
     */
    public function get_document_id(string $title): int
    {
        $query = new WP_Query(
            [
                'post_type'              => 'document',
                'title'                  => $title,
                'posts_per_page'         => 1,
                'no_found_rows'          => true,
                'ignore_sticky_posts'    => true,
                'update_post_term_cache' => false,
                'update_post_meta_cache' => false,
                'orderby'                => 'post_date ID',
                'order'                  => 'ASC',
            ]
        );

        return $query->post->ID ?? 0;
    }

    /**
     * Apply icons to document links
     *
     * @param array $matches
     *
     * @return string
     */
    public function apply_icons(array $matches): string
    {
        $link = $matches[0];
        $url = $matches[1];
        $text = $matches[2];

        // Check if the URL string contains the word "documents"
        if (stripos($url, '/documents/') !== false) {
            // track a context_object id for the icon, ensure it is not in use
            $context_id = $this->get_context_id_unique();

            // Set a JSON object JS will use to construct a context box
            $this->set_context_object($context_id, $url, $text);

            // Wrap the link with a span element containing the context ID
            return '<span class="doc-subscribe-link" id="' . $context_id . '">' . $link. '</span>';
        }

        return $link;
    }

    /**
     * Register the REST route for document subscriptions
     */
    public function register_rest_route(): void
    {
        register_rest_route(
            'document-subscriptions/v1',
            '/subscribe/(?P<id>\d+)',
            [
                'methods'  => 'POST',
                'callback' => [$this, 'subscribe'],
                'permission_callback' => '__return_true',
                'args' => [
                    'id' => [
                        'validate_callback' => function($param, $request, $key) {
                            return is_numeric( $param );
                        }
                    ],
                ]
            ]
        );
    }

    /**
     * Subscribe to document updates
     *
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_REST_Response
     */
    public function subscribe(WP_REST_Request $request): WP_Error|WP_REST_Response
    {
        $post_id = $request->get_param('id');
        $email = $request->get_param('email');

        if (empty($post_id)) {
            return new WP_Error('no_id', 'There was no ID provided', ['status' => 400]);
        }
        if (empty($email)) {
            return new WP_Error('no_email', 'No email address was provided', ['status' => 400]);
        }

        // Validate the email address
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new WP_Error('invalid_email', 'That was an invalid email address', ['status' => 400]);
        }
        // Check if the email domain is allowed
        $email_domain = substr(strrchr($email, "@"), 1);
        if (!in_array($email_domain, $this->allowed_email_domains)) {
            return new WP_Error('invalid_domain', 'The email domain is not allowed', ['status' => 400]);
        }

        $document_subscriptions = get_post_meta($post_id, 'document_subscriptions', true);
        if (empty($document_subscriptions)) {
            $document_subscriptions = [];
        }

        // Check if the email is already subscribed
        if (in_array($email, $document_subscriptions)) {
            return new WP_REST_Response(['message' => 'Your email is already subscribed'], 200);
        }

        // Add the email to the subscriptions
        $document_subscriptions[] = $email;
        // Update the post meta with the new subscriptions
        $document_subscriptions = array_unique($document_subscriptions);
        update_post_meta($post_id, 'document_subscriptions', $document_subscriptions);

        return new WP_REST_Response(['message' => 'You have subscribed successfully'], 200);
    }
}
