<?php

namespace MOJIntranet;

require_once 'prior-party-banner-events.php';

defined('ABSPATH') || exit;

class PriorPartyBannerEmail
{

    use PriorPartyBannerTrackEvents;

    /**
     * @var array contains all available banners
     */
    private array $banners = [];

    /**
     * @var string defines tha name of the ACF repeater field
     */
    private string $repeater_name = 'prior_political_party_banners';


    /**
     * @var string the name of the page for viewing banner posts
     */
    private string $menu_slug = 'prior-party-banners-email';

    private array $post_type_labels = [];


    public function __construct()
    {
        add_action('admin_menu', [$this, 'emailMenu']);
    }

    /**
     * Loaded via a hook
     *
     * @return void
     */

    public function pageLoad(): void
    {

        // Get all banners from the repeater field.
        $all_banners = get_field($this->repeater_name, 'option');

        // Map the banners to a more usable format - epoch timestamps are used for comparison.
        $mapped_banners = array_map(
            fn ($banner) => [
                'banner_active' => $banner['banner_active'],
                'banner_content' => $banner['banner_content'],
                'reference' => $banner['reference'],
                'start_epoch' => strtotime($banner['start_date']),
                'end_epoch' => $banner['end_date'] ? strtotime($banner['end_date']) : null
            ],
            $all_banners
        );

        // Only include active banners where the end date is in the past.
        $active_banners = array_filter(
            $mapped_banners,
            fn ($banner) =>  $banner['banner_active'] === true && $banner['end_epoch'] && ($banner['end_epoch'] <= time())
        );

        $this->banners = $active_banners;

        // Get the labels.
        $this->post_type_labels = [
            'post' => get_post_type_object('post'),
            'news' => get_post_type_object('news'),
            'page' => get_post_type_object('page'),
            'note-from-antonia' => get_post_type_object('note-from-antonia')
        ];
    }

    /**
     * Creates a menu link under the Tools section in the admin Dashboard
     *
     * @return void
     */
    public function emailMenu(): void
    {
        $title = 'Prior Party Digests';
        $hook = add_submenu_page(
            'tools.php',
            $title,
            $title,
            'manage_options',
            $this->menu_slug,
            [$this, 'emailPage'],
            8
        );

        add_action("load-$hook", [$this, 'pageLoad']);
    }


    /**
     * The content for the email digests page.
     * 
     * Here, we can render the upcoming email and previous 9 day's emails.
     * 
     * @return void
     */

    public function emailPage(): void
    {
        $is_after_nine = date('H', time()) >= 9;

        if ($is_after_nine) {
            $time_window_start = strtotime('today 09:00');
        } else {
            $time_window_start = strtotime('yesterday 09:00');
        }

        $email_index = 0;

        // Loop over the last 10 days.
        while ($email_index < 10) {

            // Get the offset in seconds.
            $offset = $email_index * 86400;

            // Start is the offset + the start of the time window.
            $from = $time_window_start - $offset;

            // End is start + 24 hours, minus 1 second.
            $to =  $from + 86400 - 1;

            // Get the email digest for this time window.
            $email = $this->getEmailDigestByTimes($from, $to);

            // Echo out the email.
            echo '<h2>Subject: ' . $email['subject'] . '</h2>';
            echo '<p>' . $email['body'] . '</p>';
            echo '<hr/>';

            $email_index++;
        }
    }

    /**
     * Converts a banner array (that's been populated with relevant details) to a string for email.
     * 
     * @param array $banner
     * 
     * @return string
     */

    public function bannerArrayToText(array $banner): string
    {
        $string = sprintf('<strong>%s</strong> <br/>', $banner['banner_content']);

        foreach ($banner['post_types'] as $post_type) {
            $string .= sprintf('<strong>%s</strong>: %d opt-in, %d opt-out <br/>', $post_type['label'], $post_type['true_count'], $post_type['false_count']);
        }
        $string .= sprintf('<strong>Total changes</strong>: %s <br/>', $banner['change_count']);

        $string .= sprintf('<a href="%s">Review</a>', $banner['review_url']);

        return $string;
    }

    /**
     * A helper function to get the banner by a given timestamp.
     * 
     * @param int $timestamp
     * 
     * @return null|array
     */

    public function getBannerByTimestamp(int $timestamp): null | array
    {
        // Do any banners coincide with this date?
        $banners = array_filter(
            $this->banners,
            fn ($banner) => $timestamp >= $banner['start_epoch'] && $timestamp <= $banner['end_epoch']
        );

        // If there are more than one banner, log an error. Possibly send to Sentry.
        if (sizeof($banners) > 1) {
            error_log('More than one banner is active for this date. Check the ACF settings.');
        }

        // If there is not exactly 1 banner, return.
        if (sizeof($banners) !== 1) {
            return null;
        }

        // Reset index.
        return array_values($banners)[0];
    }

    /**
     * Get the email digest for a given time range.
     * 
     * @param int $from
     * @param int $to
     * 
     * @return array the email associative array with a subject and body.
     */

    public function getEmailDigestByTimes(int $from, int $to): array
    {
        $events = $this->getTrackEvents(null, $from, $to);

        $base_query_strings = [
            'page' => 'prior-party-banners',
            'review_tracked_events' => 'true',
            // Shorthand to conditionally add array entries.
            ...($from ? ['events_from' => $from] : []),
            ...($to ? ['events_to' => $to] : []),
        ];

        // Assign to a local variable. This is mutable and will have it's entries updated.
        $banners = $this->banners;

        /**
         * Start a loop over all the banners.
         * 
         * In the loop, $banner is mutable and will affect $banners.
         */

        foreach ($banners as &$banner) {

            $banner['post_types'] = [];

            /**
             * Start a loop over the post types.
             */

            foreach ($this->post_type_labels as $post_type => $labels) {
                // Populate the stats array with initial values.
                $banner['post_types'][$post_type] = [
                    'label' => $labels->labels->name,
                    'true_count' => 0,
                    'false_count' => 0,
                ];
            }

            /**
             * End a loop over all the post types.
             */

            /**
             * Start a loop over the events.
             */

            foreach ($events as $post_id => $post_events) {
                $post_type = get_post_type($post_id);
                // Get the date to work out which banner is relevant.
                $post_timestamp = get_the_date('U', $post_id);
                // Get banner ref based on date
                $post_banner = $this->getBannerByTimestamp($post_timestamp);
                // Continue the loop if this post's banner doesn't match the one in the outer loop.
                if (!isset($post_banner['reference']) ||  $post_banner['reference'] !== $banner['reference']) {
                    continue;
                }
                // Don't count multiple events on the same post id. Just get the last one.
                $last_action = end($post_events)['action'];
                // Get the first event for comparison.
                $first_action = $post_events[0]['action'];
                // If the first event is opposite to the last event, then no change happened.
                // e.g. if the first event is true then we can infer that the initial state was false.
                // if the last event is not equal to the first event, last_action is true
                // then there was no overall change.
                if ($first_action !== $last_action) {
                    continue;
                }
                if ($last_action === 'true') {
                    $banner['post_types'][$post_type]['true_count']++;
                } else {
                    $banner['post_types'][$post_type]['false_count']++;
                }
            }

            /**
             * End a loop over the events.
             */

            // Get the number of changes for this banner.
            $banner['change_count'] = array_reduce($banner['post_types'], fn ($c, $s) => $c + $s['true_count'] + $s['false_count'], 0);

            // Build the url for reviewing.
            $query_strings = array_merge($base_query_strings, ['ref' => $banner['reference']]);
            $banner['review_url'] = admin_url('admin.php?' . http_build_query($query_strings));

            $banner['email_body'] = $this->bannerArrayToText($banner);
        }

        /**
         * End a loop over all the banners.
         */


        // Total changes for all banners.
        $total_change_count = array_reduce($banners, fn ($c, $s) => $c + $s['change_count'], 0);

        $email_heading = sprintf('<h2>Email digest for %s to %s</h2>', date('jS F, Y - g:i a', $from),  date('jS F, Y - g:i a', $to));
        $email_bodies = array_map(fn ($b) => $b['email_body'], $banners);

        $email = [
            'subject' => sprintf('Moj Intranet - Prior Party Banner Digest %d recent changes', $total_change_count),
            'body' => $email_heading . implode('<br/>', $email_bodies)
        ];

        return $email;
    }

    // TODO: schedule task for digest emails.
}

new PriorPartyBannerEmail();
