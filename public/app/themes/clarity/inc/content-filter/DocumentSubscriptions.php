<?php

namespace MOJ\Intranet;

use WP_Query;

class DocumentSubscriptions
{
    private string $icon = '';
    private array $context_objects = [];
    private array $context_ids = [];
    private int $loop_protect = 0;

    public function __construct()
    {
        $this->hooks();
    }

    public function hooks()
    {
        add_filter('acf_the_content', [$this, 'append_option_links'], 100, 1);
        add_filter('the_content', [$this, 'append_option_links'], 100, 1);
    }

    public function append_option_links($content): string
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

    public function set_context_object($context_id, $url, $name): void
    {
        $this->context_objects[] = (object) [
            'id' => $this->get_document_id($name),
            'url' => $url,
            'text' => ucfirst($name),
            'context' => $context_id
        ];
    }

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

    public function get_document_id($title): int
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

    public function apply_icons($matches) {
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
}