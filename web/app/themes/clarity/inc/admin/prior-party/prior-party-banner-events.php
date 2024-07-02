<?php

namespace MOJIntranet;

use WP_Query;


defined('ABSPATH') || exit;

trait PriorPartyBannerTrackEvents
{

    /**
     * @var string the name of the event timestamp meta field
     */
    private string $event_timestamp_field = '_prior_party_banner_event_timestamp';

    /**
     * @var string the name of the event details meta field
     */
    private string $event_details_field = '_prior_party_banner_event_details';

    /**
     * Create a new track event.
     *
     * @param bool $value The value.
     * @param int $post_id The post ID.
     * 
     * @return void
     */

    public function createTrackEvent(bool $value, int $post_id): void
    {

        $time = time();

        $timestamp_id = add_metadata('post', $post_id, $this->event_timestamp_field, $time);

        $new_event = [
            'action' => $value ? 'true' : 'false',
            'time' => $time,
            'user_id' => get_current_user_id(),
            'timestamp_id' => $timestamp_id
        ];

        add_metadata('post', $post_id, $this->event_details_field, $new_event);
    }

    /**
     * A helper function to filter existing track events by time.
     * 
     * @param array $events The events.
     * @param int|null $from The start time.
     * @param int|null $to The end time.
     * 
     * @return array
     */

    public function filterTrackEvents(array $events, int | null $from = null, int | null $to = null): array
    {
        // We don't need to filter the details by time.
        if (!$to && !$from) {
            return $events;
        }

        // Filter the details to only include events that are within the time range.
        $filtered_events = array_filter($events, function ($event) use ($from, $to) {
            // There is a from date, and the event is before the from date.
            if ($from && $event['time'] < $from) {
                return false;
            }
            // There is a to date, and the event is after the to date.
            if ($to && $event['time'] > $to) {
                return false;
            }
            // The event is within the time range.
            return true;
        });

        return array_values($filtered_events);
    }

    /**
     * Get track events.
     * 
     * Accepts optional arguments for post_id, from, and to.
     * 
     * @param int|null $post_id The post ID.
     * @param int|null $from The start time.
     * @param int|null $to The end time.
     * 
     * @return array
     */

    public function getTrackEvents(int | null $post_id = null, int | null $from = null, int | null $to = null): array
    {

        /**
         * A post_id was passed, so we only need to get the details for that post.
         */
        if ($post_id) {
            $all_details = get_metadata('post', $post_id, $this->event_details_field);

            return [$post_id => $this->filterTrackEvents($all_details, $from, $to)];
        }

        /**
         * A post_id was not passed, so return results for multiple posts.
         */

        // The base query arguments.
        $wp_query_args = [
            'fields' => 'ids',
            'posts_per_page'    => -1,
            'post_type' => ['post', 'page', 'news', 'note-from-antonia'],
            'post_status' => ['publish', 'pending'],
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => $this->event_timestamp_field,
                    'compare' => 'EXISTS',
                ],
                [
                    'key' => $this->event_timestamp_field,
                    'compare' => '!=',
                    'value' => ''
                ]
            ]
        ];

        // Add clause for date from.
        if ($from !== null) {
            $wp_query_args['meta_query'][] = array(
                'key'     => $this->event_timestamp_field,
                'value'   => $from,
                'compare' => '>=',
                'type'    => 'NUMERIC'
            );
        }

        // And, clause for date to.
        if ($to !== null) {
            $wp_query_args['meta_query'][] = array(
                'key'     => $this->event_timestamp_field,
                'value'   => $to,
                'compare' => '<=',
                'type'    => 'NUMERIC'
            );
        }

        // Get the posts.
        $posts = new WP_Query($wp_query_args);

        // Create an array to store the filtered track events.
        $all_post_events = [];

        foreach ($posts->posts as $post_id) {
            $all_events = get_metadata('post', $post_id, $this->event_details_field);
            $all_post_events[$post_id] = $this->filterTrackEvents($all_events, $from, $to);
        }

        return $all_post_events;
    }

    // TODO: lifecycle policy, delete events older than x? Or keep only the most recent x events per post?
    
    // TODO: schedule task for digest emails.

    // TODO: Update the admin page view to be filtered and include a column with recent changes
    // This link will then be sent in the digest emails.
}

