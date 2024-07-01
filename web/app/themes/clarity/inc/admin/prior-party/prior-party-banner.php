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
        // Do not run this code in the admin area.
        if (is_admin()) {
            return;
        }

        // The ACF field for the 'Prior Party Banner' checkbox has.
        $fields = acf_get_field_group($this->page_field_group_name);

        // Use the locations to determine where the banner should be displayed.
        $this->locations = $fields['location'];

        // Set the current timestamp.
        $this->time_context = time();

        // Get all banners from the repeater field.
        $all_banners = get_field($this->repeater_name, 'option') ?? [];

        // Only include active banners where the end date is in the past.
        $active_banners = array_filter(
            $all_banners,
            fn ($banner) => $banner['banner_active'] === true && strtotime($banner['end_date']) <= $this->time_context
        );

        // Map the banners to a more usable format - epoch timestamps are used for comparison.
        $this->banners = array_map(
            fn ($banner) => [
                'start_epoch' => strtotime($banner['start_date']),
                'end_epoch' => strtotime($banner['end_date'] . " +1 day") - 1,
                'banner_content' => $banner['banner_content']
            ],
            $active_banners
        );

        // Finally, add a hook to display the banner.
        add_action('before_rich_text_block', [$this, 'maybeAddBannerBeforeRichText']);
    }

    /**
     * A helper function to return if any entries of an array return true for the callback.
     * 
     * @param array $arr
     * @param callable $predicate
     * @return bool
     */

    public function arrayAny(array $arr, callable $predicate): bool
    {
        foreach ($arr as $e) {
            if (call_user_func($predicate, $e)) {
                return true;
            }
        }

        return false;
    }

    /**
     * A helper function to return true only if all entries of an array return true for the callback.
     * 
     * @param array $arr
     * @param callable $predicate
     * @return bool
     */

    public function arrayEvery(array $arr, callable $predicate): bool
    {
        foreach ($arr as $e) {
            if (!call_user_func($predicate, $e)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the current post matches an ACF location rule.
     * 
     * @param array $location
     * @return bool
     */

    public function locationMatchesPost($location): bool
    {

        if ($location['param'] === 'post_type' && $location['operator'] === '==') {
            return $location['value'] == get_post_type(get_the_ID());
        }

        if ($location['param'] === 'post_type' && $location['operator'] === '!=') {
            return $location['value'] != get_post_type(get_the_ID());
        }

        if ($location['param'] === 'post_template' && $location['operator'] === '==') {
            return $location['value'] == get_page_template_slug(get_the_ID());
        }

        if ($location['param'] === 'post_template' && $location['operator'] === '!=') {
            return $location['value'] != get_page_template_slug(get_the_ID());
        }

        throw new \Error('A location rule was not handled');
    }

    /**
     * Given all of the location rules for an ACF field group, determine if the current post is a valid location.
     * 
     * @return bool
     */

    public function isValidLocation(): bool
    {

        // Are we at a location where the banner could be displayed? Any location group must return true.
        $match = $this->arrayAny(
            $this->locations,
            // Every rule in a location group must return true.
            fn ($locations_group) => $this->arrayEvery($locations_group, [$this, 'locationMatchesPost'])
        );

        // If is a match return true.
        return $match;
    }

    /**
     * Add a banner before the rich text block.
     * 
     * This function will return void, but will output the banner if the conditions are met.
     * 
     * @return void
     */

    public function maybeAddBannerBeforeRichText(): void
    {
        // Get the post ID.
        $post_id = get_the_ID();

        // Are we at a location where the banner could be displayed?
        $valid_location = $this->isValidLocation($post_id);

        // If we are not at a valid location, return.
        if ($valid_location === false) {
            return;
        }

        // Return if an editor has opted-out of the banner.
        if (get_field($this->post_field_name) === false) {
            return;
        }

        // Get the published date.
        $date = get_the_date('U', get_the_ID());

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
