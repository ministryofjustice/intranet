<?php

$campaign_hub_banner_url = get_field('campaign_hub_url') ?? '';
$campaign_hub_image_id = get_field('campaign_hub_banner') ?? '';
$campaign_hub_banner = wp_get_attachment_image_src($campaign_hub_image_id, 'full');
$campaign_hub_banner_alt_text = get_field('campaign_hub_banner_alt_text') ?? '';
$campaign_on_off_button = get_field('campaign_hub_on_off_button') ?? false;

if ($campaign_on_off_button) { ?>
<!-- c-campaign-hub-banner starts here -->
<section class="c-campaign-hub-banner">
    
    <a href="<?= esc_url($campaign_hub_banner_url) ?>">
    <img src="<?= $campaign_hub_banner[0] ?? '' ?>" class="campaign-banner" alt="<?= esc_attr($campaign_hub_banner_alt_text); ?>" />
    </a>
    
</section>
<!-- c-campaign-hub-banner ends here -->
    <?php
}
