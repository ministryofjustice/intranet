<?php

if (!defined('ABSPATH')) {
    die();
}

/**
 * Blogroll
 * 
 * This class s all about the blogroll template, 
 * that is currently used for the following post types:
 * - Note from Amy 'note-from-amy'
 * - Note from Antonia 'note-from-antonia'
 *
 * @package Clarity
 */

class Blogroll
{

    const CONTENT_PAGE_MAP = [
        'note-from-amy' => 'notes-from-amy',
        'note-from-antonia' => 'notes-from-antonia'
    ];

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        // Hook into wp_insert_post action.
        // This will fire on new post open, save, publish, update
        add_action('wp_insert_post', [$this, 'handleNotesFromInsert'], 10, 2);

        // Create a 1 minute schedule
        add_filter('cron_schedules', [$this, 'addOneMinuteCronSchedule']);

        // Hook into template redirect
        add_action('template_redirect', [$this, 'redirectToContentPage']);
    }

    /**
     * Adds a custom cron schedule of 1 minute.
     *
     * @param array $schedules
     * @return array
     */
    public function addOneMinuteCronSchedule($schedules)
    {
        $schedules['one_minute'] = [
            'interval' => 60,
            'display' => esc_html__('Every Minute')
        ];

        return $schedules;
    }

    /**
     * Handle notes-from-* post creation or updates
     * 
     * When a note is created or updated, we need to copy the agencies 
     * from the content page to the individual note.
     * 
     * @param int $post_id
     * @param WP_Post $post
     * @return void
     */

    public function handleNotesFromInsert($post_id, $post)
    {
        if (in_array($post->post_type, ['note-from-amy', 'note-from-antonia'])) {
            $this->copyAgenciesToNotes($post->post_type, $post_id);
        }
    }


    // Hook into this post_type, we need to detect
    // new notes and apply the agencies that have
    // access to the main page...

    /**
     * Copy tagged agencies from 'Notes from Amy' page to individual Notes.
     *
     * Agencies have the ability to include content on their own Intranets. If they
     * choose Notes from Amy then each individual Note will need to reflect
     * this, otherwise it won't show up in search results for them.
     *
     * @param null $note
     */
    function copyAgenciesToNotes($post_type = null, $post_id = null)
    {

        if (!$post_type || !in_array($post_type, ['note-from-amy', 'note-from-antonia'])) {
            return;
        }

        $agencies = [];
        $post_ids = [$post_id];

        $content_page = $this::CONTENT_PAGE_MAP[$post_type];

        // get agencies attached to the page
        // this is our source of truth...
        $page = get_page_by_path($content_page);
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
     * Redirects from a single blogroll post, 
     * e.g. a single 'Note from Amy' or 'Note from Antonia'
     * to a content page that contains all notes of that type.
     * 
     * @return void
     */
    public function redirectToContentPage()
    {
        // Map the post type (array keys) to the destination URL (array values)
        $redirect_map = [
            'note-from-amy' => '/notes-from-amy/#note-%d',
            'note-from-antonia' => '/notes-from-antonia/#note-%d',
        ];

        // Only run on single post pages and if the post type is in the redirect map
        if (!is_single() || !isset($redirect_map[get_post_type()])) {
            return;
        }

        // Redirect to the page that contains all notes
        // and append the ID of the note to the URL
        wp_redirect(home_url(sprintf($redirect_map[get_post_type()], get_the_ID())), 301);
        exit;
    }
}

new Blogroll();
