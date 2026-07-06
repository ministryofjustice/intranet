<?php

namespace MOJ\Intranet\GcoeFeedApi;

defined('ABSPATH') || exit;

trait PageContent
{
    public $document_url_regex = '';


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
    public function getDocumentsFromContent(string $content): array
    {
        if(!$this->document_url_regex) {
            // Ge the home host from the home URL.
            $home_host = parse_url(get_home_url(), PHP_URL_HOST);
    
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
