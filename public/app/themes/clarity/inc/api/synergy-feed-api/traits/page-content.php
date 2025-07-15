<?php

namespace MOJ\Intranet;

defined('ABSPATH') || exit;

trait PageContent
{
    public $content = null;

    public $hooks_added = false;

    /**
     * A list of page templates, where the content is stored as markdown exclusively in the 'content' field.
     * 
     * For pages with these templates, the content can be returned as markdown or html, from the `$page` property `post_content`.
     */
    const MARKDOWN_TEMPLATES = [
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

        return 'html' === $format ?
            apply_filters('the_content', $page->post_content) :
            $page->post_content;
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
        if($this->hooks_added) {
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

        add_action('clarity_after_content', function () use (&$content) {
            // 🅱️ End the nested output buffer and capture the content.
            $this->content = trim(ob_get_clean());
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
}
