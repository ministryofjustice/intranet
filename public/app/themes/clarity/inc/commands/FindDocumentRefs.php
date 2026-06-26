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

            // Output toggles.
            // $verbose      = per-row progress / not-found logging.
            // $show_summary = the counts summary block at the end.
            // $write_csv    = write the referenced / unreferenced / broken-link CSVs.
            $verbose      = false;
            $show_summary = true;
            $write_csv    = true;

            global $wpdb;

            /**
             * Search the database for document references in post content and metadata.
             */

            $results = $wpdb->get_results("
                SELECT ID, post_content AS content, 'post' AS type, '' AS source
                FROM wp_posts
                WHERE 1=1
                AND post_status = 'publish'
                AND post_content LIKE '%/documents/%'

                UNION ALL

                SELECT post_id AS ID, meta_value AS content, 'metadata' AS type, '' AS source
                FROM wp_postmeta
                WHERE 1=1
                AND meta_value LIKE '%/documents/%'
                AND post_id IN (
                    SELECT ID
                    FROM wp_posts
                    WHERE 1=1
                    AND post_status = 'publish'
                )

                UNION ALL

                SELECT option_id AS ID, option_value AS content, 'option' AS type, option_name AS source
                FROM wp_options
                WHERE 1=1
                AND option_value LIKE '%/documents/%'
            ");

            // Init an array to store the documents and their references.
            $documents = [];

            // Broken document links
            $broken_links = [];

            // Diagnostic counters surfaced in the summary output.
            $stats = [
                'rows_total'              => count($results),
                'rows_post'               => 0,
                'rows_meta'               => 0,
                'rows_option'             => 0,
                'parts_total'             => 0,
                'resolved_by_full_url'    => 0,
                'resolved_by_slug'        => 0,
                'resolved_by_slug_db'     => 0,
                'unresolved'              => 0,
                'unresolved_target_agency' => 0,
                'skipped_no_terms'        => 0,
                'skipped_other_agency'    => 0,
            ];

            foreach ($results as $result) {
                if ($result->type === 'post') {
                    $stats['rows_post']++;
                } elseif ($result->type === 'metadata') {
                    $stats['rows_meta']++;
                } else {
                    $stats['rows_option']++;
                }
                if ($verbose) {
                    if ($result->type === 'post') {
                        WP_CLI::line("Processing Post: {$result->ID}");
                    } elseif ($result->type === 'metadata') {
                        WP_CLI::line("Processing Metadata for Post: {$result->ID}");
                    } else {
                        WP_CLI::line("Processing Option: {$result->source}");
                    }
                }

                // Split the content on each /documents/ occurrence.
                $parts = explode('/documents/', $result->content);

                // Remove the first part.
                array_shift($parts);

                // Map over the parts to clean them up.
                foreach ($parts as $part) {
                    $stats['parts_total']++;
                    // Delete everything after the first quote.
                    $part = explode(' ', $part)[0];
                    $part = explode('"', $part)[0];
                    $part = explode('\'', $part)[0];
                    $part = explode(')', $part)[0];
                    $part = explode("\n", $part)[0];
                    $part = explode("<", $part)[0];
                    $part = explode("?", $part)[0];
                    $part = explode("#", $part)[0];
                    $part = explode(">", $part)[0];
                    // Additional URL terminators (query strings, shortcodes, markdown, lists).
                    $part = explode("&", $part)[0];
                    $part = explode("]", $part)[0];
                    $part = explode(",", $part)[0];
                    $part = explode("\t", $part)[0];
                    $part = explode("\r", $part)[0];

                    // Trim all white space
                    $part = trim($part); // 2194 before this, 2187 after

                    // Get the document ID
                    $document_id = url_to_postid('/documents/' . rtrim($part, '/'));

                    if ($document_id) {
                        $stats['resolved_by_full_url']++;
                    }

                    // Try and get the document by removing the date from the URL
                    if (!$document_id) {
                        $part_segments = explode('/', $part);
                        $document_slug = end($part_segments);

                        $document_id = url_to_postid('/documents/' . $document_slug);

                        if ($document_id) {
                            $stats['resolved_by_slug']++;
                        }
                    }

                    // Authoritative fallback: url_to_postid is unreliable for the document
                    // CPT (it has no date-less rewrite rule), so look the slug up directly.
                    // This also resolves links whose URL carries a stale/wrong date.
                    if (!$document_id) {
                        $slug_candidate = preg_replace('/\.[A-Za-z0-9]{1,7}$/', '', $part);
                        $slug_segments  = explode('/', rtrim($slug_candidate, '/'));
                        $slug_candidate = end($slug_segments);

                        if ($slug_candidate !== '') {
                            $document_id = (int) $wpdb->get_var($wpdb->prepare(
                                "SELECT ID FROM {$wpdb->posts}
                                 WHERE post_name = %s
                                 AND post_type = 'document'
                                 AND post_status = 'publish'
                                 LIMIT 1",
                                $slug_candidate
                            ));

                            if ($document_id) {
                                $stats['resolved_by_slug_db']++;
                            }
                        }
                    }

                    if (!$document_id) {
                        $stats['unresolved']++;

                        // Log the $part, so we can try and manually find it.
                        if ($verbose) {
                            WP_CLI::line("Could not find document ID for: {$part}");
                        }

                        // Get the agencies for this post (option rows have no page, so skip them).
                        $terms = $result->type === 'option'
                            ? false
                            : get_the_terms($result->ID, 'agency');

                        // If there is only one agency and the slug is the same as the agency we're looking for, log the part.
                        if (is_array($terms) && count($terms) === 1 && $terms[0]->slug === $agency) {
                            $stats['unresolved_target_agency']++;
                            if ($verbose) {
                                WP_CLI::line("Document link not found: {$part}");
                                WP_CLI::line("On {$agency} page: " . get_permalink($result->ID));
                            }

                            $post_url = get_permalink($result->ID);

                            // If we're running locally, replace the URL with the production URL.
                            if (get_home_url() === 'http://intranet.docker') {
                                $post_url = str_replace('http://intranet.docker', 'https://intranet.justice.gov.uk', get_permalink($result->ID));
                            }

                            if (!isset($broken_links[$part])) {
                                $broken_links[$part] = [
                                    'locations' => [$post_url],
                                    'link' => 'https://intranet.justice.gov.uk/documents/' . $part,
                                ];
                            } else {
                                $broken_links[$part]['locations'][] = $post_url;
                            }
                        }

                        continue;
                    }

                    // Get the document's agency
                    $terms = get_the_terms($document_id, 'agency');

                    if (empty($terms)) {
                        $stats['skipped_no_terms']++;
                        continue;
                    }

                    // Filter the terms by the agency
                    $filtered_terms = array_filter($terms, function ($term) use ($agency) {
                        return $term->slug === $agency;
                    });

                    if (empty($filtered_terms)) {
                        $stats['skipped_other_agency']++;
                        continue;
                    }

                    // Option rows have no permalink; label them by option name instead.
                    if ($result->type === 'option') {
                        $post_url = "wp_option: {$result->source}";
                    } else {
                        $post_url = get_permalink($result->ID);

                        // If we're running locally, replace the URL with the production URL.
                        if (get_home_url() === 'http://intranet.docker') {
                            $post_url = str_replace('http://intranet.docker', 'https://intranet.justice.gov.uk', get_permalink($result->ID));
                        }
                    }

                    // If the document doesn't exist in the documents array, add it.
                    if (!isset($documents[$document_id])) {
                        $documents[$document_id] = [
                            'title' => get_the_title($document_id),
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

            /**
             * Summarise and write the results.
             */

            // Total links recorded across all referenced documents.
            $total_reference_links = array_sum(array_map(static function ($document) {
                return count($document['links']);
            }, $documents));

            // Denominator: all published documents tagged with this agency.
            $agency_document_ids = get_posts([
                'post_type'      => 'document',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'tax_query'      => [
                    [
                        'taxonomy' => 'agency',
                        'field'    => 'slug',
                        'terms'    => $agency,
                    ],
                ],
            ]);

            // The deliverable: agency documents that are never referenced anywhere.
            $unreferenced_ids = array_diff($agency_document_ids, array_keys($documents));

            // Referenced docs tagged for this agency but outside the published denominator
            // (e.g. private/draft). They are excluded from the unreferenced list above.
            $referenced_not_published = array_diff(array_keys($documents), $agency_document_ids);

            $stats['total_agency_documents'] = count($agency_document_ids);
            $stats['referenced_documents'] = count($documents);
            $stats['referenced_not_published'] = count($referenced_not_published);
            $stats['unreferenced_documents'] = count($unreferenced_ids);
            $stats['referenced_links']     = $total_reference_links;
            $stats['broken_links_distinct'] = count($broken_links);

            if ($show_summary) {
                WP_CLI::line('');
                WP_CLI::line("===== Counts for agency: {$agency} =====");
                foreach ($stats as $label => $value) {
                    WP_CLI::line(str_pad($label, 28) . $value);
                }
                WP_CLI::line('========================================');
            }

            if (!$write_csv) {
                return;
            }

            $local_to_prod = static function ($url) {
                return get_home_url() === 'http://intranet.docker'
                    ? str_replace('http://intranet.docker', 'https://intranet.justice.gov.uk', $url)
                    : $url;
            };

            // Open a CSV for writing with a UTF-8 BOM so Excel detects the encoding.
            $open_csv = static function ($path) {
                $fd = fopen($path, 'w');
                fwrite($fd, "\xEF\xBB\xBF");
                return $fd;
            };

            // Resolve a user ID to an email address, cached.
            $user_emails = [];
            $user_email = function ($uid) use (&$user_emails) {
                $uid = (int) $uid;
                if (!$uid) {
                    return '';
                }
                if (!isset($user_emails[$uid])) {
                    $user = get_userdata($uid);
                    $user_emails[$uid] = $user ? $user->user_email : "user {$uid}";
                }
                return $user_emails[$uid];
            };

            // Base columns for a document. "Last edited by" comes from _edit_last,
            // falling back to the author when no edit is recorded.
            $doc_row = function ($id) use ($local_to_prod, $user_email) {
                $post   = get_post($id);
                $author = $post ? (int) $post->post_author : 0;
                $editor = (int) get_post_meta($id, '_edit_last', true);
                return [
                    // Raw stored title, with any literal entities decoded (avoids the
                    // wptexturize entities that get_the_title() would inject).
                    $post ? html_entity_decode($post->post_title, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '',
                    $local_to_prod(get_permalink($id)),
                    $local_to_prod(admin_url("post.php?post={$id}&action=edit")),
                    $id,
                    $post ? $post->post_modified : '',
                    $post ? $post->post_type : '',
                    $post ? $post->post_status : '',
                    $user_email($author),
                    $user_email($editor ?: $author),
                ];
            };

            $doc_header = ['Title', 'URL', 'Edit URL', 'ID', 'Last Modified', 'Type', 'Post Status', 'User (created, or inherited by)', 'User (last edited by)'];

            /**
             * Referenced documents: document_id, title, then every referencing location.
             */
            uasort($documents, static function ($a, $b) {
                return $b['document_id'] <=> $a['document_id'];
            });

            $documents_flat = array_map(function ($document) use ($doc_row) {
                return [
                    ...$doc_row($document['document_id']),
                    ...$document['links'],
                ];
            }, $documents);

            // Header row; trailing columns are additional referencing locations.
            array_unshift($documents_flat, [...$doc_header, 'referenced_on']);

            $fd = $open_csv("/var/www/html/tmp/{$agency}_document_references.csv");
            WP_CLI\Utils\write_csv($fd, $documents_flat);
            fclose($fd);

            /**
             * Unreferenced documents: the cleanup list (document_id, title, URL).
             */
            $unreferenced_flat = array_map(function ($id) use ($doc_row) {
                return $doc_row($id);
            }, $unreferenced_ids);

            array_unshift($unreferenced_flat, $doc_header);

            $fd = $open_csv("/var/www/html/tmp/{$agency}_unreferenced_documents.csv");
            WP_CLI\Utils\write_csv($fd, $unreferenced_flat);
            fclose($fd);

            /**
             * Broken links: dead document links found on this agency's pages.
             */
            uasort($broken_links, static function ($a, $b) {
                return $b['link'] <=> $a['link'];
            });

            $broken_links_flat = array_map(static function ($link) {
                return [
                    $link['link'],
                    ...$link['locations'],
                ];
            }, $broken_links);

            // Header row; trailing columns are additional pages where the dead link was found.
            array_unshift($broken_links_flat, ['broken_link', 'found_on']);

            $fd = $open_csv("/var/www/html/tmp/{$agency}_broken_links.csv");
            WP_CLI\Utils\write_csv($fd, $broken_links_flat);
            fclose($fd);

            WP_CLI::success("Wrote CSVs to /var/www/html/tmp/ for agency: {$agency}");
        }
    }

    WP_CLI::add_command('find_doc_refs', 'FindDocumentRefs');
}
