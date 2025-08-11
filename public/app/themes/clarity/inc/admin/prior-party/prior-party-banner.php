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
     * @var string defines the key of the ACF field for the on/off toggle
     */
    private string $page_field_key = 'field_667d8a0fd14f1';

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
        add_action('before_media_grid_content', [$this, 'maybeAddBannerBeforeRichText']);
        add_action('before_note_from_jo', [$this, 'maybeAddBannerBeforeRichText']);
        add_action('before_note_from_amy', [$this, 'maybeAddBannerBeforeRichText']);
        add_action('before_note_from_antonia', [$this, 'maybeAddBannerBeforeRichText']);
        add_action('before_tabbed_content', [$this, 'maybeAddBannerBeforeRichText']);

        // Add a shortcode to display the banner.
        add_shortcode('prior-party-banner', [$this, 'renderBannerShortcode']);

        // Filter the instructions on the edit post screen.
        add_filter('acf/load_field/key=' . $this->page_field_key, [$this, 'modifyFieldInstructions']);
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
        if (is_admin() && !$doing_ajax) {
            return;
        }

        $this->loadBanners();
    }

    /**
     * Get the time context from the query string.
     * 
     * Why do we need a time context?
     * Under normal circumstances, the banner will only show if the current date is between the start and end dates.
     * This makes sense in the context of *Prior* Party Banners. We don't want to show a banner for a government that hasn't left yet.
     * ...
     * However, to preview a banner that hasn't ended yet, we need to set the time context to the day after the banner ends.
     * In effect we are time travelling the user into the future, the day after a banner's end date, so that they can see the banner.
     * 
     * This function validates the time_context query var against known end epochs.
     * Returns false if: 
     * - the user is not logged in or can't edit posts
     * - the value not in the known array
     * - or the time_context is in the past
     * 
     * @param array $known_end_epochs
     * 
     * @return false|int
     */

    public function getTimeContext(array $known_end_epochs): false | int
    {
        // Is the user logged in and can edit posts?
        if (!current_user_can('edit_posts')) {
            return false;
        }

        // If the query var is empty, return now.
        if (empty($_GET['time_context'])) {
            return false;
        }

        // Return false if the time_context is in the past - it's unnecessary.
        if ((int) $_GET['time_context'] < time()) {
            return false;
        }

        // Compare time_context against known end_epochs.
        if (in_array($_GET['time_context'], $known_end_epochs, false)) {
            return (int) $_GET['time_context'];
        }

        return false;
    }


    /**
     * Load banners from the ACF repeater field.
     * 
     * This function maps the start and end dates to epoch timestamps for comparison.
     * It also sets the time context to the current time or a preview time.
     * 
     * @return void
     */

    public function loadBanners(): void
    {
        // Get all banners from the repeater field.
        $all_banners = get_field($this->repeater_name, 'option') ?: [];

        // I think when this is run during a json api request, the field is not defined and it is erroring.
        if (empty($all_banners) || !is_array($all_banners)) {
            return;
        }

        // Map the banners to a more usable format - epoch timestamps are used for comparison.
        $mapped_banners = array_map(
            fn ($banner) => [
                'banner_active' => $banner['banner_active'],
                'banner_content' => $banner['banner_content'],
                'start_epoch' => strtotime($banner['start_date']),
                'end_epoch' => $banner['end_date'] ? (new \DateTime($banner["end_date"]))->modify('+1 day')->format('U')  : null
            ],
            $all_banners
        );

        // An array of known end dates in epoch format.
        $known_end_epochs = array_map(
            fn ($banner) => $banner['end_epoch'],
            $mapped_banners
        );

        // Set the current timestamp.
        $preview_time = $this->getTimeContext($known_end_epochs);
        // Time context will either be:
        // - 00:00:00 the day after a banner that hasn't ended yet
        // - or now
        $this->time_context = $preview_time ?: time();

        // Only include active banners where the end date is in the past.
        $active_banners = array_filter(
            $mapped_banners,
            function ($banner) {
                // If preview_unpublished is in the query string & the user can edit posts... they can see banners regardless of banner_active state.
                // Else, other users (and logged out visitors) are only shown banners that are active.
                $user_can_view = (isset($_GET['preview_unpublished']) && current_user_can('edit_posts')) || $banner['banner_active'] === true;

                return $user_can_view && $banner['end_epoch'] && ($banner['end_epoch'] <= $this->time_context);
            }
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
     * Get the banner that should be displayed for the current post.
     * 
     * This doesn't check for a valid location or if it's opted out.
     * That's so that a shortcode can be used to show the banner regardless of location or toggle value.
     * 
     * @param int $post_id
     * 
     * @return array|null
     */

    public function getBannerByPostId(int $post_id): ?array
    {
        // Get the published date.
        $date = get_the_date('U', $post_id);

        // Do any banners coincide with this date? 
        $banners = array_filter(
            $this->banners,
            // Using >= and < here to match:
            // - times after and *including* 00:00:00 on the banner start date.
            // - and times before, but *not including* 24:00:00 on the banner end date.
            fn ($banner) => $date >= $banner['start_epoch'] && $date < $banner['end_epoch']
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
        $banners = array_values($banners);

        return $banners[0];
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

        $banner = $this->getBannerByPostId($post_id);

        if (!$banner) {
            return;
        }

        // We have a banner to display.
        get_template_part('src/components/c-notification-banner/view', null, ['heading' => $banner['banner_content']]);
    }

    /**
     * Render the banner shortcode.
     * 
     * Run when the shortcode has been used in the content.
     * If there's a banner that could be displayed, render it.
     * The status of the toggle and the location rules are ignored.
     * 
     * @return void
     */

    public function renderBannerShortcode()
    {
        // Get the post ID.
        $post_id = get_the_ID();

        // Don't validate the location, leave that up to the editor.
        $banner = $this->getBannerByPostId($post_id);

        if (!$banner) {
            return;
        }

        // Filter the content to remove the p tags that are added before and after the shortcode.
        add_filter('the_content', [$this, 'filterRichTextContent'], 15);

        // We have a banner to display.
        ob_start();
        get_template_part('src/components/c-notification-banner/view', null, ['heading' => $banner['banner_content']]);
        return ob_get_clean();
    }

    /**
     * If a shortcode has been applied, then the banner will be inside a p tag. Remove the opening and closing p tags.
     * 
     * @param string $content
     * 
     * @return string
     */

    public function filterRichTextContent(string $content): string
    {
        $content = preg_replace('/<p>\s*(<!-- c-moj-banner starts here -->)/', '$1', $content);
        $content = preg_replace('/(<!-- c-moj-banner ends here -->)\s*<\/p>/', '$1', $content);

        return $content;
    }

    /**
     * Modify the instructions for the ACF field.
     * 
     * This is so that editors have a different message when there are no banners to display.
     * i.e. they've just created a page, so re-word and use the future tense.
     * 
     * @param array $field
     * 
     * @return array
     */

    public function modifyFieldInstructions(array $field): array
    {
        // Are we on a post edit screen?
        $screen = get_current_screen();
        if (!$screen || $screen->base !== 'post') {
            return $field;
        }

        // Get the published date.
        $date = get_the_date('U', get_the_ID());

        // Load the banners from the ACF field.
        $this->loadBanners();

        // Do any banners coincide with this date?
        $banners = array_filter(
            $this->banners,
            fn ($banner) => ($date >= $banner['start_epoch']) && ($date <= $banner['end_epoch'])
        );

        // If there are no banners then re-word the instructions accordingly.
        if (empty($banners)) {
            $field['ui_on_text'] = 'Yes';
            $field['ui_off_text'] = 'No';
            $field['instructions'] = 'When a different government is is elected, should we show a ';
            $field['instructions'] .= 'banner to inform visitors that this content was published under a prior government?';
        }

        return $field;
    }
}

new PriorPartyBanner();
