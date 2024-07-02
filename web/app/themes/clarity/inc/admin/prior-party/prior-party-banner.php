<?php

namespace MOJIntranet;

class PriorPartyBanner
{
    /**
     * @var int the current timestamp
     */
    private int $time_context = 0;

    /**
     * @var array contains all available banners
     */
    private array $banners = [];

    /**
     * @var array contains all locations where the banner should be displayed
     */
    private array $locations = [];

    /**
     * @var string defines the name of the ACF repeater field
     */
    private string $repeater_name = 'prior_political_party_banners';

    /**
     * @var string defines the name of the ACF field group where the on/off toggle is
     */
    private string $page_field_group_name = 'group_667d8a0f642b5';

    /**
     * @var string defines the name of the ACF field on the posts
     */
    private string $post_field_name = 'prior_party_banner';

    public function __construct()
    {
        $this->hooks();
    }

    public function hooks(): void
    {
        add_action('init', [$this, 'init']);
        // Finally, add a hook to display the banner.
        add_action('before_rich_text_block', [$this, 'maybeAddBannerBeforeRichText']);
        add_action('before_note_from_antonia', [$this, 'maybeAddBannerBeforeRichText']);
    }

    public function getPreviewTime(array $known_end_epochs): false | int
    {
        // Is the user logged in and can edit posts?
        if (!current_user_can('edit_posts')) {
            return false;
        }

        // If the query var is empty, return now.
        if (empty($_GET['time_context'])) {
            return false;
        }

        // Compare time_context against known end_epochs.
        if (in_array($_GET['time_context'], $known_end_epochs,  false)) {
            return (int) $_GET['time_context'];
        }

        return false;
    }

    public function init(): void
    {
        // The ACF field for the 'Prior Party Banner' checkbox has.
        $fields = acf_get_field_group($this->page_field_group_name);

        // Use the locations to determine where the banner should be displayed.
        $this->locations = $fields['location'];

        // Are we doing AJAX, needed for Notes from Antonia lazy load.
        $doing_ajax = defined('DOING_AJAX') && DOING_AJAX;

        // During AJAX requests, is_admin will be true. Return here if we're at an admin screen and not doing AJAX.
        if ( is_admin() && !$doing_ajax) {
            return;
        }

        // Get all banners from the repeater field.
        $all_banners = get_field($this->repeater_name, 'option');

        // I think when this is run during a json api request, the field is not defined and it is erroring.
        // TODO - look into this.
        if (gettype($all_banners) !== 'array') {
            return;
        }

        // Map the banners to a more usable format - epoch timestamps are used for comparison.
        $mapped_banners = array_map(
            fn ($banner) => [
                'banner_active' => $banner['banner_active'],
                'banner_content' => $banner['banner_content'],
                'start_epoch' => strtotime($banner['start_date']),
                'end_epoch' => $banner['end_date'] ? strtotime($banner['end_date']) : null
            ],
            $all_banners
        );

        // An array of known end dates in epoch format.
        $known_end_epochs = array_map(
            fn ($banner) => $banner['end_epoch'],
            $mapped_banners
        );

        // Set the current timestamp.
        $this->time_context =  $this->getPreviewTime($known_end_epochs) ?: time();

        // Only include active banners where the end date is in the past.
        $active_banners = array_filter(
            $mapped_banners,
            fn ($banner) =>  $banner['banner_active'] === true && $banner['end_epoch'] && ($banner['end_epoch'] <= $this->time_context)
        );

        $this->banners = $active_banners;
    }

    /**
     * A helper function to return if any entries of an array return true for the callback.
     *
     * @param array    $arr
     * @param int      $post_id
     * @param callable $predicate
     *
     * @return bool
     */

    public function arrayAny(array $arr, int $post_id, callable $predicate): bool
    {
        foreach ($arr as $e) {
            if (call_user_func($predicate, $e, $post_id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * A helper function to return true only if all entries of an array return true for the callback.
     *
     * @param array    $arr
     * @param int      $post_id
     * @param callable $predicate
     *
     * @return bool
     */

    public function arrayEvery(array $arr, int $post_id, callable $predicate): bool
    {
        foreach ($arr as $e) {
            if (!call_user_func($predicate, $e, $post_id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the current post matches an ACF location rule.
     *
     * @param array $location
     * @param int   $post_id
     *
     * @return bool
     */

    public function locationMatchesPost(array $location, int $post_id): bool
    {
        /**
         * Post type
         */
        if ($location['param'] === 'post_type' && $location['operator'] === '==') {
            return $location['value'] == get_post_type($post_id);
        }

        if ($location['param'] === 'post_type' && $location['operator'] === '!=') {
            return $location['value'] != get_post_type($post_id);
        }

        /**
         * Post template
         */
        if ($location['param'] === 'post_template' && $location['operator'] === '==') {
            return $location['value'] == get_page_template_slug($post_id);
        }

        if ($location['param'] === 'post_template' && $location['operator'] === '!=') {
            return $location['value'] != get_page_template_slug($post_id);
        }

        throw new \Error('A location rule was not handled');
    }

    /**
     * Given all the location rules for an ACF field group, determine if the current post is a valid location.
     *
     * @param $post_id
     *
     * @return bool
     */
    public function isValidLocation($post_id): bool
    {

        // Are we at a location where the banner could be displayed? Any location group must return true.
        return $this->arrayAny(
            $this->locations,
            $post_id,
            // Every rule in a location group must return true.
            fn ($locations_group) => $this->arrayEvery($locations_group, $post_id, [$this, 'locationMatchesPost'])
        );
    }

    /**
     * Add a banner before the rich text block.
     *
     * This function will return void, but will output the banner if the conditions are met.
     *
     * @param null $post_id
     *
     * @return void
     */

    public function maybeAddBannerBeforeRichText($post_id = null): void
    {
        // Get the post ID.
        $post_id = $post_id ?: get_the_ID();

        // Are we at a location where the banner could be displayed?
        $valid_location = $this->isValidLocation($post_id);

        // If we are not at a valid location, return.
        if ($valid_location === false) {
            return;
        }

        // Return if an editor has opted-out of the banner.
        if (get_field($this->post_field_name, $post_id) === false) {
            return;
        }

        // Get the published date.
        $date = get_the_date('U', $post_id);

        // Do any banners coincide with this date?
        $banners = array_filter(
            $this->banners,
            fn ($banner) => $date >= $banner['start_epoch'] && $date <= $banner['end_epoch']
        );

        // If there are more than one banner, log an error. Possibly send to Sentry.
        if (sizeof($banners) > 1) {
            error_log('More than one banner is active for this date. Check the ACF settings.');
        }

        // If there is not exactly 1 banner, return.
        if (sizeof($banners) !== 1) {
            return;
        }

        // Reset index.
        $banners = array_values($banners);

        // We have a banner to display.
        get_template_part('src/components/c-notification-banner/view', null, ['heading' => $banners[0]['banner_content']]);
    }
}

new PriorPartyBanner();
