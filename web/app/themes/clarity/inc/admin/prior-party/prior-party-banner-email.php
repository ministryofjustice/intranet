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

    private string $user_field_name = 'prior_political_party_banners_digest_users';

    private string $bcc_field_name = 'prior_political_party_banners_digest_bcc';


    /**
     * @var string the name of the page for viewing banner posts
     */
    private string $menu_slug = 'prior-party-banners-email';

    private array $post_type_labels = [];


    public function __construct()
    {

        // Hook into an ACF field on the Settings page - echo the content of the email page.
        add_filter('acf/load_field/key=field_668e41dd45027', [$this, 'placeholderField']);

        // Schedule the email digest.
        // This will run the maybeSendEmails function twice a day.
        // During winter, this will be 8am and 9am in the UK.
        // During summer, this will be 9am and 10am in the UK.
        // Running twice will ensure that one is run at 9am UK time.
        // Params:
        // - Unix timestamp (UTC) for when to next run the event.
        // - How often the event should subsequently recur.
        // - Action hook to execute when the event is run.

        $args = ['dst' => true];
        if (!wp_next_scheduled('prior_party_banner_email_cron_hook', $args)) {
            wp_schedule_event(strtotime('08:02:00'), 'daily', 'prior_party_banner_email_cron_hook', $args);
        }

        $args = ['dst' => false];
        if (!wp_next_scheduled('prior_party_banner_email_cron_hook', $args)) {
            wp_schedule_event(strtotime('09:02:00'), 'daily', 'prior_party_banner_email_cron_hook', $args);
        }

        add_action('prior_party_banner_email_cron_hook', [$this, 'maybeSendEmails']);
    }

    public function placeholderField($field)
    {
        // Are we on the options page? 
        // It's important not to filter on the ACF > Fields Groups > Setting page.
        $screen = get_current_screen();
        if (is_admin() && $screen->base === 'tools_page_prior-party-settings') {
            $field['label'] = '';
            ob_start();
            $this->emailPage();
            $field['message'] = ob_get_clean();
            return $field;
        }

        return $field;
    }

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
     * A timezone aware function that will send digest emails to those set in the Options page.
     * 
     * @param ?array $props An optional array containing the DST status.
     * 
     * @return void
     */

    public function maybeSendEmails(?array $props = []): void
    {
        // Is London currently observing DST?
        $in_dst = strtotime("today 9:00 am Europe/London") !== strtotime("today 9:00 am UTC");

        // If the current DST status doesn't match the props, return early.
        if (isset($props['dst']) && $props['dst'] !== $in_dst) {
            // It's 10am in the summer or 8am in the winter - don't send emails.
            return;
        }

        $timestamp_from = strtotime('yesterday 9:00 Europe/London');
        $timestamp_to = strtotime('today 9:00 Europe/London');
        $email_content = $this->getEmailDigestByTimes($timestamp_from, $timestamp_to);

        if (empty($email_content)) {
            return;
        }

        // Was an email passed in manually - for testing purposes?
        if (isset($props['recipient'])) {
            wp_mail($props['recipient'], $email_content['subject'], $email_content['body']);
            echo 'A test email was sent to email sent to ' . $props['recipient'];
            // Return early.
            return;
        }

        // Get all recipients - from both Users and BCC fields.
        $all_recipients = [
            // Spread the arrays into a single array.
            ...(get_field($this->user_field_name, 'option') ?: []),
            ...(get_field($this->bcc_field_name, 'option') ?: [])
        ];

        if (!empty($all_recipients)) {
            // Get all the emails - from the User objects and the BCC rows.
            $all_emails = array_map(fn ($row) => $row['user_email'], $all_recipients);
            // Send the email(s).
            wp_mail($all_emails, $email_content['subject'], $email_content['body']);
        }
    }

    /**
     * Load the banners and post type labels.
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
     * The content for the email digests page.
     * 
     * Here, we can render the upcoming email and previous 9 day's emails.
     * Administrators can also trigger an email to be sent, by adding ?send=<test-email> to the URL.
     * 
     * @return void
     */

    public function emailPage(): void
    {
        // Load the page.
        $this->pageLoad();

        // Manually trigger an email to be sent.
        if ($_GET['send'] && is_email(urldecode($_GET['send'])) && current_user_can('administrator')) {
            $this->maybeSendEmails(['recipient' => urldecode($_GET['send'])]);
        }

        $nine_am_today = strtotime('today 09:00 Europe/London');
        $is_after_nine = time() > $nine_am_today;

        if ($is_after_nine) {
            $time_window_start = $nine_am_today;
        } else {
            $time_window_start = strtotime('yesterday 09:00 Europe/London');
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

            if ($email) {
                // Echo out the email.
                echo '<h2>Subject: ' . $email['subject'] . '</h2>';
                echo '<pre>' . $email['body'] . '</pre>';
                echo '<hr/>';
            }

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
        $string = sprintf("Banner: %s \n", $banner['banner_content']);

        foreach ($banner['post_types'] as $post_type) {
            if (!$post_type['true_count'] && !$post_type['false_count']) {
                continue;
            }
            $string .= sprintf("%s: %d opt-in, %d opt-out\n", $post_type['label'], $post_type['true_count'], $post_type['false_count']);
        }
        $string .= sprintf("Total changes: %s\n", $banner['change_count']);

        $string .= sprintf("Review: %s", $banner['review_url']);

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
     * @return array|null the email associative array with a subject and body - or null for no changes.
     */

    public function getEmailDigestByTimes(int $from, int $to): array|null
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

        // Filter out banners with no changes.
        $banners = array_filter($banners, fn ($banner) => $banner['change_count'] > 0);

        // Return early if there are no banners.
        if (empty($banners)) {
            return null;
        }

        // Total changes for all banners.
        $total_change_count = array_reduce($banners, fn ($c, $s) => $c + $s['change_count'], 0);

        $local_from_date = $this->timestampToLocalDateObject($from);
        $local_to_date = $this->timestampToLocalDateObject($to);

        $email_heading = sprintf("Email digest for %s to %s\n---\n", $local_from_date->format('jS F - g:i a'),  $local_to_date->format('jS F - g:i a'));
        $email_bodies = array_map(fn ($b) => $b['email_body'], $banners);

        $email = [
            'subject' => sprintf('Moj Intranet - Prior Party Banner Digest %d recent changes', $total_change_count),
            'body' => $email_heading . implode("\n---\n", $email_bodies)
        ];

        return $email;
    }
}

new PriorPartyBannerEmail();
