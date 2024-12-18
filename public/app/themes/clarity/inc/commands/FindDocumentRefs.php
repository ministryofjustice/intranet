<?php

/**
 * FindDocumentRefs
 * 
 * This command will search for document references in post content and metadata.
 * 
 * Usage:
 * wp find_doc_refs <agency_slug>
 * 
 * Example:
 * wp find_doc_refs hmcts
 */

if (defined('WP_CLI') && WP_CLI) {
    class FindDocumentRefs
    {
        public function __invoke($args, $assoc_args)
        {
            $agency = $args[0];

            if (!$agency) {
                WP_CLI::error('Please provide an agency slug.');
                return;
            }

            global $wpdb;

            /**
             * Search the database for document references in post content and metadata.
             */

            $results = $wpdb->get_results("
                SELECT ID, post_content AS content, 'post' AS type
                FROM wp_posts
                WHERE 1=1
                AND post_status = 'publish'
                AND post_content LIKE '%/documents/____/__/%'

                UNION ALL

                SELECT post_id AS ID, meta_value AS content, 'metadata' AS type
                FROM wp_postmeta
                WHERE 1=1
                AND meta_value LIKE '%/documents/____/__/%'
                AND post_id IN (
                    SELECT ID
                    FROM wp_posts
                    WHERE 1=1
                    AND post_status = 'publish'
                )
            ");

            // Init an array to store the documents and their references.
            $documents = [];

            foreach ($results as $result) {
                if ($result->type === 'post') {
                    WP_CLI::line("Processing Post: {$result->ID}");
                } else {
                    WP_CLI::line("Processing Metadata for Post: {$result->ID}");
                }

                // Split the post content at the /documents/____/__/
                $parts = explode('/documents/', $result->content);

                // Remove the first part.
                array_shift($parts);

                // Map over the parts to clean them up.
                foreach ($parts as $part) {
                    // Delete everything after the first quote.
                    $part = explode(' ', $part)[0];
                    $part = explode('"', $part)[0];
                    $part = explode('\'', $part)[0];
                    $part = explode(')', $part)[0];

                    // Get the document ID
                    $document_id = url_to_postid('/documents/' . rtrim($part, '/'));

                    if (!$document_id) {
                        continue;
                    }

                    // Get the document's agency
                    $terms = get_the_terms($document_id, 'agency');

                    if (empty($terms)) {
                        continue;
                    }

                    // Filter the terms by the agency
                    $filtered_terms = array_filter($terms, function ($term) use ($agency) {
                        return $term->slug === $agency;
                    });

                    if (empty($filtered_terms)) {
                        continue;
                    }

                    $post_url = get_permalink($result->ID);

                    // If we're running locally, replace the URL with the production URL.
                    if (get_home_url() === 'http://intranet.docker') {
                        $post_url = str_replace('http://intranet.docker', 'https://intranet.justice.gov.uk', get_permalink($result->ID));
                    }

                    // If the document doesn't exist in the documents array, add it.
                    if (!isset($documents[$document_id])) {
                        $documents[$document_id] = [
                            'document_id' => $document_id,
                            'links' => [$post_url]
                        ];
                        continue;
                    }

                    // If the document exists in the documents array, add the post URL to the links array.
                    if (
                        !in_array($post_url, $documents[$document_id]['links'], true)
                    ) {
                        $documents[$document_id]['links'][] = $post_url;
                    }
                }
            }

            // Sort the documents by document_id
            uasort($documents, static function ($a, $b) {
                return $b['document_id'] <=> $a['document_id'];
            });

            // Flatten the documents array
            $documents_flat = array_map(static function ($document) {
                return [
                    $document['document_id'],
                    ...$document['links']
                ];
            }, $documents);

            // Write the CSV
            $fd = fopen("/var/www/html/tmp/{$agency}_document_references.csv", 'w');
            WP_CLI\Utils\write_csv($fd, $documents_flat);
            fclose($fd);
        }
    }

    WP_CLI::add_command('find_doc_refs', 'FindDocumentRefs');
}
