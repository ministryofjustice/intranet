<?php

namespace MOJIntranet;

use WP_Query;

class PriorPartyBanner
{

    /**
     * @var string defines tha name of the ACF repeater field
     */
    private string $repeater_name = 'prior_political_party_banners';

    public function __construct()
    {
        add_action('before_rich_text_block', [$this, 'addBannerBeforeRichText']);
    }

    public function getBanner($post_id)
    {

        // Based on the post_id get the correct banner.

        return 'This was published under the 2015 to 2024 Conservative government';
    }

    public function addBannerBeforeRichText()
    {
        $heading = $this->getBanner(get_the_ID());

        get_template_part('src/components/c-notification-banner/view', null, ['heading' => $heading]);
    }
}

new PriorPartyBanner();
