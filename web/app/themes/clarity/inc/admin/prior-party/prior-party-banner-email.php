<?php

namespace MOJIntranet;

use WP_Query;

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
        $this->banners = get_field($this->repeater_name, 'option');
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
            'edit_posts',
            $this->menu_slug,
            [$this, 'emailPage'],
            8
        );

        add_action("load-$hook", [$this, 'pageLoad']);
    }

    public function emailPage(): void
    {

        // $time_of_email = 

        // $offset_days = isset($_GET['offset_days']) ? (int) $_GET['offset_days'] : 0;
        // $start = 

        // // Get all of the events for the previous 10 days.



        // $events = $this->getTrackEvents(null, strtotime('-10 days'), time());

        // // Loop over the 10 days
        // // For each day, loop over the events and build a digest.

        // echo '<pre>';
        // print_r($events);
        // echo '</pre>';

        // $email_time = "09:00";

        // echo ' email content';

        // $email_time_today = strtotime("today " .  $email_time);

        $this->getDigestByTimes(0, 20000000000000);
    }

    public function getDigestByTimes(int $from, int $to)
    {
        $events = $this->getTrackEvents(null, $from, $to);

        $post_ids = array_keys($events);

        // Group them by banner reference.
        $grouped = [];

        foreach ($this->banners as $banner) {
            $grouped[$banner['reference']] = [];
        }




        echo '<pre>';
        print_r($events);
        echo '</pre>';
    }

    // TODO: schedule task for digest emails.
}

new PriorPartyBannerEmail();
