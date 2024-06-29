<?php

namespace MOJIntranet;

use WP_Query;

class PriorPartyBanner
{
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

    public function __construct()
    {
        // The ACF field for the 'Prior Party Banner' checkbox has.
        $fields = acf_get_field_group($this->page_field_group_name);

        // Use the locations to determine where the banner should be displayed.
        $this->locations = $fields['location'];

        // Get all banners from the repeater field.
        $all_banners = get_field($this->repeater_name, 'option') ?? [];

        // Filter out the banners that are not active.
        $active_banners = array_filter(
            $all_banners,
            fn ($banner) => $banner['banner_active'] === true
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
        add_action('before_rich_text_block', [$this, 'addBannerBeforeRichText']);
    }


    /**
     * A helper function to return true only if all entries of an array return true for the callback.
     */

    public function arrayEvery(array $arr, callable $predicate)
    {
        foreach ($arr as $e) {
            if (!call_user_func($predicate, $e)) {
                return false;
            }
        }

        return true;
    }

    public function locationMatchesPost($location)
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

    public function isValidLocation()
    {

        // Are we at a location where the banner could be displayed? Every rule in a location group must return true.
        $location_matches = array_filter(
            $this->locations,
            fn ($locations_group) => $this->arrayEvery($locations_group, [$this, 'locationMatchesPost'])
        );

        // If there are matches return true.
        return sizeof($location_matches) ? true : false;
    }

    public function addBannerBeforeRichText()
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
        if (get_field('prior_party_banner') === false) {
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

        // If there are no banners, return.
        if (sizeof($banners) !== 1) {
            return;
        }

        // reset index
        $banners = array_values($banners);

        // We have a banner to display.
        get_template_part('src/components/c-notification-banner/view', null, ['heading' => $banners[0]['banner_content']]);
    }
}

new PriorPartyBanner();
