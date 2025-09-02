<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

trait PageContent
{
    public $home_url = '';

    public $content = null;

    public $hooks_added = false;

    public $document_url_regex = '';

    /**
     * A list of page templates, where the content is stored as markdown exclusively in the 'content' field.
     * 
     * For pages with these templates, the content can be returned as markdown or html, from the `$page` property `post_content`.
     */
    const MARKDOWN_TEMPLATES = [
        '', // An empty string is used to match the default page template.
        'page.php',
        'page_generic.php', // This template file doesn't exist, and pages with this template will use the default page template.
    ];

    /**
     * A list of page templates that use ACF to store the content.
     * 
     * These templates have their content stored in ACF fields, and the content is returned as HTML, or mixed markdown and HTML.
     * There is no single 'content' field, so we make use of the template files in order to get the content.
     * 
     * The following templates must have the following hooks, in order to capture the page's main content:
     * - `clarity_before_content`
     * - `clarity_after_content`
     */
    const ACF_TEMPLATES = [
        'page_campaign_content.php',
        'page_campaign_landing.php',
        'page_guidance_and_support.php',
    ];


    /**
     * Get the content of the page.
     *
     * @param object $page The page object.
     * @param string $format The preferred format to return the content in, either 'html' or 'markdown'.
     * @return string|null An associative array with the content and format, or null if the page template is not found.
     */
    public function getPageContent($page, $format = 'html'): string | null
    {
        $page_template = get_page_template_slug($page->ID);

        if (in_array($page_template, self::MARKDOWN_TEMPLATES)) {
            // If the page template is in the markdown templates, return the content in the preferred format.
            return  $this->getContentFromMarkdownTemplate($page, $format);
        }

        if (in_array($page_template, self::ACF_TEMPLATES)) {
            // If the page template is in the ACF templates, return the content from the ACF template as html.
            return $this->getContentFromAcfTemplate($page, $page_template, $format);
        }

        error_log('page id ' . $page->ID . ' has an unsupported template: ' . $page_template);

        return null;
    }


    /**
     * Get the content of the page when using a markdown template.
     * 
     * @param object $page The page object.
     * @param string $format The preferred format to return the content in, either 'html' or 'markdown'.
     * @return string
     */
    public function getContentFromMarkdownTemplate($page, $format = 'html'): string
    {

        $content = 'html' === $format ?
            apply_filters('the_content', $page->post_content) :
            $page->post_content;

        // Remove html comments from the content.
        $content = preg_replace('/<!--(.*?)-->/', '', $content);

        // Trim the content to remove any leading or trailing whitespace.
        $content = trim($content);

        return $content;
    }


    /**
     * Add the hooks to capture the content of the page.
     * 
     * This method sets up the hooks that will be used to capture the content of the page.
     * It uses output buffering to capture the content of the page template, and stores it in the `$content` property.
     * 
     * @return void
     */
    public function addHooksForContent(): void
    {
        if ($this->hooks_added) {
            // If the hooks have already been added, then return early.
            return;
        }

        // These 2 hooks will be fired when a template is included with, `include get_template_directory() ...`
        // The running order is: 1️⃣, 🅰️, 🅱️, 2️⃣.
        add_action('clarity_before_content', function () {
            // Reset the content property to null, so that we can clear any previous content.
            $this->content = null;
            // 🅰️ Start a nested output buffer, this is used to capture the page's main content.
            ob_start();
        });

        add_action('clarity_after_content', function () {
            // 🅱️ End the nested output buffer and capture the content.
            $this->content = ob_get_clean();
        });

        // Set the hooks_added property to true, so that we don't add the hooks again.
        $this->hooks_added = true;
    }


    /**
     * Get the content of the page when using a template that makes use of ACF fields.
     * 
     * Use output buffering to capture the content from the page template.
     * The idea is that using output buffering will avoid the need to duplicate the logic of the template.
     * 
     * @param object $page The page object.
     * @param string $page_template The page template slug.
     * @return string|null
     */
    public function getContentFromAcfTemplate($page, $page_template, $format = 'html'): string|null
    {
        // Use the global $wp_query, so that the template and components can be used without modification.
        global $wp_query;

        // Create a new WP_Query object with the page ID and post type.
        $wp_query = new \WP_Query(['p' => $page->ID, 'post_type' => 'page']);

        // Create a global post with the page data, this is so that the template can use it.
        global $post;
        // Assign the page object to the global post variable.
        $post = $page;
        // Set up the post data for the template to use.
        setup_postdata($post);

        // If the preferred format is markdown, then remove 2 filters that would otherwise convert the content to html.
        if ('markdown' === $format) {
            $this->removeMarkdownFilters();
        }

        // If the hooks have not been added yet, then add them.
        if (!$this->hooks_added) {
            $this->addHooksForContent();
        }

        // 1️⃣ Start the primary output buffer to capture the content.
        //    Without this, the html header would be echoed out.
        ob_start();

        include get_template_directory() . '/' . $page_template;

        // 2️⃣ End the primary output buffer.
        //    It's unnecessary to capture the output here, as we are using the actions 
        //    to strategically capture a specific part of the content.
        ob_end_clean();

        // Reset the global post data
        wp_reset_postdata();

        // Assign content to a local variable, so that we clear the class's content property.
        $content = $this->content;

        // Remove html comments from the content.
        $content = preg_replace('/<!--(.*?)-->/', '', $content);

        // Trim the content to remove any leading or trailing whitespace.
        $content = trim($content);

        // Clear the content property, so that it can be reused.
        $this->content = null;

        // Reset the global query
        $wp_query = null;

        // Reset the global post variable to null.
        $post = null;

        // Finally, return the content.
        return $content;
    }


    /**
     * Remove the markdown filters that are added by the php-markdown-extra plugin.
     * 
     * This is necessary to prevent the markdown content from being transformed into HTML.
     * 
     * @return void
     */
    public function removeMarkdownFilters(): void
    {
        remove_filter('acf_the_content', 'wpautop');

        if (!class_exists('Michelf\Bootstrap')) {
            // If the class does not exist, then we cannot remove the filters.
            return;
        }

        $this->remove_class_object_filter(
            'the_content',
            'Michelf\Bootstrap',
            'markdownPost'
        );

        $this->remove_class_object_filter(
            'the_content',
            'Michelf\Bootstrap',
            'markdown'
        );

        $this->remove_class_object_filter(
            'the_excerpt',
            'Michelf\Bootstrap',
            'markdown'
        );

        $this->remove_class_object_filter(
            'acf_the_content',
            'Michelf\Bootstrap',
            'markdown'
        );
    }


    /**
     * Get documents (of document post type) from page content.
     *
     * This function retrieves document URLs from the content of a page.
     * It uses a regular expression to find all document links in the content.
     *
     * Example content of absolute, relative, and excluded links:
     * [Test 1](http://intranet.docker/documents/2025/08/developer-test.pdf)
     * [Test 2](https://intranet.docker/documents/2025/08/developer-test.pdf)
     * [Test 3](//intranet.docker/documents/2025/08/developer-test.pdf)
     * [Test 4](/documents/2018/05/gdpr-privacy-notice-for-employees-workers-and-contractors-uk.docx)
     * /documents/2025/08/developer-test.pdf
     * Some /documents/2025/08/developer-test.pdf text
     * <a href="http://intranet.docker/documents/2025/08/developer-test.pdf">Test 5</a>
     * <a href='http://intranet.docker/documents/2025/08/developer-test.pdf'>Test 6</a>
     * <a href='/documents/2025/08/developer-test.pdf'>Test 7</a>
     * <a href="/documents/2025/08/developer-test.pdf">Test 8</a>
     * [Exclude test 1](https:example.com/documents/2025/08/developer-test.pdf)
     * <a href="https:example.com/documents/2025/08/developer-test.pdf">Exclude test 2</a>
     * <a href='https:example.com/documents/2025/08/developer-test.pdf'>Exclude test 3</a> 
     *
     * @param string $home_url The home URL of the site, used to match document URLs.
     * @param string $content The content of the page to search for document links.
     * @return array An array of document URLs found in the content.
     */
    public function getDocumentsFromContent(string $home_url, string $content): array
    {
        if(!$this->document_url_regex) {
            // Ge the home host from the home URL.
            $home_host = parse_url($home_url, PHP_URL_HOST);
    
            // Absolute URLs start with //, followed by the home host. http or https before the // can be ignored.
            $absolute_url_prefix = '(?:' . preg_quote('//' . $home_host, '/') . ')';
            
            // Relative URLs start with one of the following characters: (, ', " or whitespace.
            $relative_url_prefix = '[\(\'"\s]';
    
            // Combine the absolute and relative URL prefixes to create a regex pattern that matches both.
            $url_prefix = '(?:' . $absolute_url_prefix . '|' .  $relative_url_prefix . ')';
    
            // All document urls are proceeded with one of the following characters: ), ', " or whitespace.
            $url_close = '[^\'"\)\s]+';
    
            // Create the regex pattern to match document URLs.
            $this->document_url_regex = '/' . $url_prefix . '(\/documents\/' . $url_close . ')/i';
        }

        // If the content is empty, return an empty array.
        if (empty($content)) {
            return [];
        }

        // Use the document URL regex to find all document links in the content.
        preg_match_all($this->document_url_regex, $content, $matches);

        // If no matches are found, return an empty array.
        if (empty($matches[1])) {
            return [];
        }

        // Deduplicate the matches to avoid processing the same document multiple times.
        $document_urls = array_unique($matches[1]);

        // Map the document URLs to their IDs.
        $document_ids = array_map('url_to_postid', $document_urls);

        // Filter out any invalid document IDs (0 means no post found).
        $document_ids = array_filter($document_ids, fn ($id) => $id !== 0);

        return $document_ids;
    }
}
