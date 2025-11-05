<?php

if (!defined('ABSPATH')) {
    die();
}

/**
 * Blogroll
 * 
 * This class is all about the blogroll template that is currently used for the following post types:
 * - Note from Jo 'note-from-jo'
 * - Note from Amy 'note-from-amy'
 * - Note from Antonia 'note-from-antonia'
 *
 * @package Clarity
 */

class Blogroll
{
    // Map of post types (array key) to content pages (array value)
    const CONTENT_PAGE_MAP = [
        'note-from-jo' => 'notes-from-jo',
        'note-from-amy' => 'notes-from-amy',
        'note-from-antonia' => 'notes-from-antonia'
    ];

    const POST_TYPE_ARRAY = [
        'note-from-jo',
        'note-from-amy',
        'note-from-antonia'
    ];

    // Name of the cron job
    const CRON_HOOK = 'blogroll_cron_hook';

    // Which user roles are allowed to view archived pages?
    const ARCHIVE_PERMISSIONS = [
        'agency_admin',
        'administrator',
    ];

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        // Hook into template redirect, to redirect from a single post to the content page.
        add_action('template_redirect', [$this, 'redirectToContentPage']);

        // Hook into template redirect, to redirect some visitors from an archived Perm. Sec. page to the current one.
        add_action('template_redirect', [$this, 'maybeRedirectFromArchivedPage']);

        // Hook into wp_insert_post action.
        // This will fire on new post open, save, publish, update
        add_action('wp_insert_post', [$this, 'handleNotesFromInsert'], 10, 2);

        // Create a 1 minute schedule
        add_filter('cron_schedules', [$this, 'addOneMinuteCronSchedule']);

        // Schedule the cron job
        add_action('init', [$this, 'scheduleCronJob']);

        // Hook into the cron job to copy agencies to notes.
        add_action($this::CRON_HOOK, [$this, 'copyAgenciesToNotesCronHandler']);
    }

    /**
     * Redirects from a single blogroll post, 
     * e.g. a single 'Note from Amy' or 'Note from Antonia'
     * to a content page that contains all notes of that type.
     * 
     * @return void - returns void or exits if a redirect is performed.
     */
    public function redirectToContentPage(): void
    {
        // Only run on single post pages and if the post type is in the redirect map
        if (!is_single() || !isset($this::CONTENT_PAGE_MAP[get_post_type()])) {
            return;
        }

        // Redirect to the page that contains all notes
        // and append the ID of the note to the URL
        wp_redirect(home_url($this::CONTENT_PAGE_MAP[get_post_type()] . '#note-' . get_the_ID()), 301);
        exit;
    }

    /**
     * Redirects from an archived page to the current one.
     * This is used when a Perm. Sec. page is archived and the user
     * does not have the required permissions to view it.
     * 
     * @return void - returns void or exits if a redirect is performed.
     */
    public function maybeRedirectFromArchivedPage(): void
    {
        // Check if the current page the blogroll template
        if (!is_page_template('page_blogroll.php')) {
            return;
        }

        // Check if the current user has the required permissions
        if (current_user_can('agency_admin') || current_user_can('administrator')) {
            return;
        }

        // Check if the current page is archived
        $is_archived = get_post_meta(get_the_ID(), 'is_archived', true);
        if (!$is_archived) {
            return;
        }

        // Get the redirect URL
        $redirect_url = get_post_meta(get_the_ID(), 'archive_redirect', true);
        if (empty($redirect_url)) {
            return;
        }

        // Redirect to the new URL, use 302 so that the redirect 
        // is not cached when an Agency Admin is logged out.
        wp_redirect(get_the_permalink($redirect_url), 302);
        exit;
    }


    /**
     * Handle notes-from-* post creation or updates
     * 
     * When a note is created or updated, we need to copy the agencies 
     * from the content page to the individual note.
     * 
     * @param int     $post_id
     * @param WP_Post $post
     *
     * @return void
     */
    public function handleNotesFromInsert(int $post_id, WP_Post $post): void
    {
        if (in_array($post->post_type, $this::POST_TYPE_ARRAY)) {
            $this->copyAgenciesToNotes($post->post_type, $post_id);
        }
    }

    /**
     * Cron handler for copying agencies to notes.
     * 
     * This function is called by the cron job and will copy the agencies
     * from the content page to all notes of that type.
     * Loops over the post types and calls the copyAgenciesToNotes function.
     * 
     * @return void
     */
    public function copyAgenciesToNotesCronHandler(): void
    {
        // Get all notes
        $post_types = $this::POST_TYPE_ARRAY;
        foreach ($post_types as $post_type) {
            $this->copyAgenciesToNotes($post_type);
        }
    }

    /**
     * Copy tagged agencies from parent page to individual Notes.
     *
     * Agencies have the ability to include content on their own Intranets. If they
     * choose Notes from Amy then each individual Note will need to reflect
     * this, otherwise it won't show up in search results for them.
     *
     * @param null $post_type
     * @param null $post_id
     */
    function copyAgenciesToNotes($post_type = null, $post_id = null): void
    {

        if (!$post_type || !in_array($post_type, $this::POST_TYPE_ARRAY)) {
            return;
        }

        $agencies = [];
        $post_ids = [$post_id];

        $content_page = $this::CONTENT_PAGE_MAP[$post_type];

        // get agencies attached to the page
        // this is our source of truth...
        $page = get_page_by_path($content_page);

        // Check if the page exists
        if (!$page) {
            trigger_error("Content page (/$content_page) for post type $post_type not found.");
            return;
        }

        foreach (wp_get_object_terms($page->ID, 'agency') as $agency) {
            $agencies[] = $agency->slug;
        }

        if (!$post_id) {
            // get all notes
            $post_ids = get_posts([
                'post_type' => $post_type,
                'numberposts' => -1,
                'fields' => 'ids'
            ]);
        }

        foreach ($post_ids as $post_id) {
            // check if agencies match the current saved agencies...
            $terms = get_the_terms($post_id, 'agency');
            $agencies_current = [];
            foreach ($terms as $agency) {
                $agencies_current[] = $agency->slug;
            }

            // we are checking if the agency arrays are different
            // if they are, we will make changes, otherwise, do nothing.
            if (!empty(array_diff($agencies, $agencies_current))) {
                // set as defined
                $terms = wp_set_object_terms($post_id, $agencies, 'agency');

                if (is_wp_error($terms)) {
                    trigger_error("Terms could not be added for a note with an ID of: " . $post_id);
                }
            }
        }
    }


    /**
     * Adds a custom cron schedule of 1 minute.
     *
     * @param array $schedules
     * @return array
     */
    public function addOneMinuteCronSchedule(array $schedules): array

    {
        $schedules['one_minute'] = [
            'interval' => 60,
            'display' => esc_html__('Every Minute')
        ];

        return $schedules;
    }

    /**
     * Schedules a cron job to run the copyAgenciesToNotes function.
     * 
     * This function checks if the cron job is already scheduled,
     * and if not, schedules it to run every 1 minute.
     * 
     * @return void
     */
    public function scheduleCronJob(): void
    {
        // Check if the cron job is already scheduled
        if (!wp_next_scheduled($this::CRON_HOOK)) {
            // Schedule the cron job to run every 1 minute
            wp_schedule_event(
                time(),
                (getenv('WP_ENV') === 'production' ? 'twicedaily' : 'one_minute'),
                $this::CRON_HOOK
            );
        }
    }
}

new Blogroll();
