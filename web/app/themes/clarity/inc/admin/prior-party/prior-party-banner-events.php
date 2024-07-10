<?php

namespace MOJIntranet;

use MOJ\Intranet\Agency;
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
     * @var int the maximum age of an event in days, before it is deleted
     */
    private int $max_age_in_days = 365;

    /**
     * A function to convert a timestamp to a local date object.
     * 
     * @param int $timestamp The timestamp.
     * 
     * @return \DateTime
     */

    public function timestampToLocalDateObject(int $timestamp): \DateTime
    {
        $date_object = new \DateTime();
        $date_object->setTimezone(new \DateTimeZone('Europe/London'));
        $date_object->setTimestamp($timestamp);
        return $date_object;
    }

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
     * Delete a track event.
     * 
     * This function deletes 2 rows from the post_meta table.
     * Similar to how 2 are created when a track event is created.
     * 
     * @param int $post_id The post ID.
     * @param array $event The event.
     * 
     * @return void
     */

    public function deleteTrackEvent(int $post_id, array $event): void
    {
        delete_metadata_by_mid('post', $event['timestamp_id']);
        delete_metadata('post', $post_id, $this->event_details_field, $event);
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

    public function getLatestEvent($post_id): array
    {
        $events = $this->getTrackEvents($post_id);
        $events = array_reverse($events[$post_id] ?? []);

        if (!empty($events[0])) {
            $event = $events[0];
            $user = get_user_by('id', $event['user_id']);
            $agencies = wp_get_object_terms($user->ID, 'agency');

            $agency_name = 'No Agency';
            foreach ($agencies as $agency) {
                if (property_exists($agency, 'name')) {
                    $agency_name = $agency->name;
                }
            }

            $local_date = $this->timestampToLocalDateObject($event['time']);

            return [
                'name' => $user->display_name ?: 'Unknown',
                'local_date' => $local_date->format('jS F Y'),
                'local_time' => $local_date->format('H:i'),
                'agency' => $agency_name,
                'action' => $event['action'] === 'true' ? 'displayed' : 'removed',
                'tracked' => true
            ];
        }

        return [
            'tracked' => false
        ];
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

        if ($from && $from < strtotime('-' . $this->max_age_in_days . ' days')) {
            error_log('The events returned by getTrackEvents may be truncated due to old ones being deleted.');
        }

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

    /**
     * Transform the event array into a readable format.
     *
     * @param array $event The event.
     *
     * @return string
     */
    public function eventToReadableFormat(array $event): string
    {
        if (empty($event)) {
            return '';
        }

        $local_date = $this->timestampToLocalDateObject($event['time']);
        $local_time = $local_date->format($this->date_format_time);
        $user = get_user_by('id', $event['user_id']);
        $user_name = $user ? $user->display_name : 'Unknown';

        return "User: $user_name,<br/> Action: {$event['action']},<br/> Time: $local_time";
    }

    /**
     * Delete old track events.
     * 
     * This function will delete all events older than the max age.
     * 
     * @return void
     */

    public function deleteOldTrackEvents(): void
    {
        // Get the expiry date in timestamp format.
        $expiry_timestamp = strtotime('-' . $this->max_age_in_days . ' days');

        // Get all events older than the expiry date.
        $old_events = $this->getTrackEvents(null, null, $expiry_timestamp);

        // Delete the old events, except the last one.
        foreach ($old_events as $post_id => $events) {
            // Remove the last event from the array.
            array_pop($events);
            foreach ($events as $event) {
                $this->deleteTrackEvent($post_id, $event);
            }
        }
    }
}
