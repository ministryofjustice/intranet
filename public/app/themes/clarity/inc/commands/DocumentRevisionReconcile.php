<?php
/**
 * Usage:
 *  dry-run: wp fix-document-revisions
 *  real-run: wp fix-document-revisions fix-documents
 */

class DocumentRevisionReconcile
{
    /**
     * @var array
     */
    public array $posts;

    /**
     * @var wpdb|QM_DB
     */
    public wpdb|QM_DB $db;

    /**
     * @var false
     */
    private bool $dry_run;

    private array $reportable;

    private array $succeeded;

    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;
        $this->dry_run = true;
    }

    public function __invoke($args): void
    {
        error_reporting(0);

        $do_fix = $args[0] ?? '';

        if ($do_fix === 'fix-documents') {
            $this->dry_run = false;
        }

        $this->active();
    }

    private function active(): void
    {
        $this->warning();
        $this->prepare();
        $this->collect();
        $this->update();
        $this->report();
    }

    private function prepare(): void
    {
        $query = 'SELECT `ID`, `post_title`
                    FROM `wp_posts`
                    WHERE `post_type` LIKE \'document\'
                        AND `post_content` LIKE \'\'
                        AND `post_status` LIKE \'publish\'';

        $this->posts = $this->db->get_results($query);
        $this->message(['%d broken documents were found.' => count($this->posts)]);
    }

    private function collect(): void
    {
        $counter = 0;
        foreach ($this->posts as $key => $post) {
            $parent_id = $this->db->get_var("SELECT ID FROM `wp_posts` WHERE `post_parent` = $post->ID AND `post_type` LIKE 'attachment'");
            if (is_numeric($parent_id)) {
                $this->posts[$key]->attachment = $parent_id;
                $counter++;
            }
        }

        $this->message(['%d documents have stray revisions.' => $counter]);
    }

    private function update(): void
    {
        foreach ($this->posts as $key => $post) {
            // let's check the attachment property
            $attachment_is_set = false;
            if (property_exists($post, 'attachment') && is_numeric($post->attachment)) {
                $attachment_is_set = true;
            }

            if ($this->dry_run === false) {
                $result = 0;
                if ($attachment_is_set) {
                    // run the update
                    $result = $this->db->update(
                        'wp_posts',
                        ['post_content' => '<!-- WPDR ' . $post->attachment . ' -->'],
                        ['ID' => $post->ID]
                    );
                }

                if ($result === 1) {
                    $this->succeeded($post);
                    $this->message(['Update succeeded for: %s' => $post->post_title], 'success');
                } else {
                    $this->reportable($post);
                    $this->message(['Update failed with ID %d' => $post->ID], 'warning');
                }

                continue;
            }

            if ($key === 0) {
                $this->message("Dry run activated.");
                $this->message("Checking queries...");
            }

            // what would have happened?
            if ($attachment_is_set) {
                $this->succeeded($post);
                $this->message([
                    'Update succeeded for: %s' => "UPDATE `wp_posts` SET `post_content` = '<!-- WPDR $post->attachment -->' WHERE `ID` = $post->ID"
                ],
                    'success'
                );
            } else {
                $this->reportable($post);
                $this->message(['Update failed with ID %d' => $post->ID], 'warning');
            }
        }
    }

    private function report(): void
    {
        if (!empty($this->reportable)) {
            $message = "Documents have been flagged unfixable by the Intranet. We identified them in a recent attempt to fix 403 errors.\n\n";
            $message .= "Editors created the following documents without uploading an artefact.\n\n";
            foreach ($this->reportable as $errored) {
                $message .= "_____________________________________\n{$errored['agency']}: {$errored['title']}\n- {$errored['admin_url']}\n\n";
            }

            $this->message($message);
            wp_mail('damien.wilson@digital.justice.gov.uk,rhian.townsend@digital.justice.gov.uk', 'Document Revisions: unfixable', $message);
        }

        if (!empty($this->succeeded)) {
            $message = "Documents have been successfully repaired. We identified them in a recent effort to fix 403 errors.\n\n";
            foreach ($this->succeeded as $repaired) {
                $message .= "_____________________________________\n{$repaired['agency']}: {$repaired['title']}\n- {$repaired['link']}\n- {$repaired['admin_url']}\n\n";
            }

            $this->message($message);
            wp_mail('damien.wilson@digital.justice.gov.uk,rhian.townsend@digital.justice.gov.uk', 'Document Revisions: repaired', $message);
        }
    }

    private function warning(): void
    {
        if (!$this->dry_run) {
            $this->message("\nAre you sure you want to continue?", 'confirm');
            $this->message("\n---------------------------------\n");
            $this->message("We will attempt to fix all document revisions.\n", 'warning');

            sleep(3);

            $this->message("\n---------------------------------\n");
            $this->message("Beginning repair effort now...");
        }
    }

    private function succeeded($post): void
    {
        $this->succeeded['moj_' . $post->ID] = [
            'link' => get_the_permalink($post->ID),
            'title' => $post->post_title,
            'admin_url' => get_admin_url($post->ID) . "post.php?post=$post->ID&action=edit",
            'agency' => get_the_terms($post->ID, 'agency')[0]->name
        ];
    }

    private function reportable($post): void
    {
        $this->reportable['moj_' . $post->ID] = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'admin_url' => get_admin_url($post->ID) . "post.php?post=$post->ID&action=edit",
            'agency' => get_the_terms($post->ID, 'agency')[0]->name
        ];
    }

    private function message($message, $status = 'log'): void
    {
        if (is_array($message)) {
            $message = sprintf(key($message), $message[key($message)]);
        }

        switch ($status) {
            case 'confirm':
                WP_CLI::confirm($message);
                break;
            case 'success':
                WP_CLI::success($message);
                break;
            case 'warning':
                WP_CLI::warning($message);
                break;
            case 'log':
                WP_CLI::log($message);
                break;

        }
    }
}

// 1. Register the instance for the callable parameter.
$instance = new DocumentRevisionReconcile();
WP_CLI::add_command('fix-document-revisions', $instance);

// 2. Register object as a function for the callable parameter.
WP_CLI::add_command('fix-document-revisions', 'DocumentRevisionReconcile');
