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
     * @param int|null $limit How many events to return (sorted by newest first).
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
     * @param int|null $limit How many events to return for each post (sorted by newest first).
     *
     * @return array
     */
    public function getTrackEvents(int | null $post_id = null, int | null $from = null, int | null $to = null, int | null $limit = null): array
    {

        if ($from && $from < strtotime('-' . $this->max_age_in_days . ' days')) {
            error_log('The events returned by getTrackEvents may be truncated due to old ones being deleted.');
        }

        /**
         * A post_id was passed, so we only need to get the details for that post.
         */
        if ($post_id) {
            $all_details = get_metadata('post', $post_id, $this->event_details_field);
            $filtered_events = $this->filterTrackEvents($all_details, $from, $to);

            if ($limit) {
                $filtered_events = array_slice($filtered_events, $limit * -1, $limit);
            }

            return [$post_id => $filtered_events];
        }

        /**
         * A post_id was not passed, so return results for multiple posts.
         */

        // The base query arguments.
        $wp_query_args = [
            'fields' => 'ids',
            'posts_per_page'    => -1,
            'post_type' => ['post', 'page', 'news', 'note-from-amy', 'note-from-antonia'],
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
            $filtered_events = $this->filterTrackEvents($all_events, $from, $to);

            if ($limit) {
                $filtered_events = array_slice($filtered_events,  $limit * -1, $limit);
            }

            $all_post_events[$post_id] = $filtered_events;
        }

        return $all_post_events;
    }

    /**
     * Populate the event details.
     * 
     * Add display name, agency and localised date and time to the event details.
     * 
     * @param array $event The event.
     * 
     * @return array
     */

    public function populateEventDetails(array $event): array
    {
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
        ];
    }

    /**
     * Get the populated track events.
     * 
     * A wrapper function that calls getTrackEvents and then populates the event details.
     * 
     * @param int|null $post_id The post ID.
     * @param int|null $from The start time.
     * @param int|null $to The end time.
     * @param int|null $limit How many events to return for each post (sorted by newest first).
     * 
     * @return array
     */

    public function getPopulatedTrackEvents(int | null $post_id = null, int | null $from = null, int | null $to = null,  int | null $limit = null): array
    {
        $events = $this->getTrackEvents($post_id, $from, $to, $limit);
        $fortified_events = [];

        foreach ($events as $post_id => $post_events) {
            $fortified_events[$post_id] = array_map([$this, 'populateEventDetails'], $post_events);
        }

        return $fortified_events;
    }

    /**
     * Get the latest event for a post.
     * 
     * A convenience function that calls getTrackEvents with a limit of 1.
     * 
     * @param int $post_id The post ID.
     * @param int|null $from The start time.
     * @param int|null $to The end time.
     * 
     * @return ?array
     */

    public function getLatestEventForPost(int $post_id, int | null $from = null, int | null $to = null): ?array
    {
        $events = $this->getTrackEvents($post_id, $from, $to, 1);

        if (!empty($events[$post_id][0])) {
            return $this->populateEventDetails($events[$post_id][0]);
        }

        return null;
    }

    /**
     * Transform the fortified event array into a readable format.
     *
     * @param array $event The event.
     *
     * @return array an associative array with redacted user, the local date and the text.
     */

    public function populatedEventToReadableFormat(?array $event): array
    {
        if (!$event) {
            return [];
        }

        // Redact name if current user is not administrator
        $name = (current_user_can('manage_options') ? $event['name'] : 'A user');

        // create the display string
        $event_data = [
            'local_date' => $event['local_date'] . ', ' . $event['local_time'],
            'text' => $name . ' from ' . $event['agency'] . ' ' . $event['action'] . ' the banner'
        ];

        return $event_data;
    }

    /**
     * Get the latest event display string.
     * 
     * @param int $post_id The post ID.
     * 
     * @return array an associative array with redacted user, the local date and the text.
     */

    private function getLatestEventDisplayString(int $post_id): array
    {
        $latest = $this->getLatestEventForPost($post_id);

        return $latest ? $this->populatedEventToReadableFormat($latest) : [];
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
