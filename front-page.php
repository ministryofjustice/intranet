<?php
/***
 *
 * Template Name: Front Page
 *
 */
get_header();

use MOJ\Intranet\Agency;
use MOJ\Intranet\HomepageBanners;

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

$emergencyBanner = HomepageBanners::getEmergencyBanner(get_intranet_code());
$fullWidthTopBanner = HomepageBanners::getTopBanner(get_intranet_code());
?>
    <div id="maincontent" class="u-wrapper l-main t-home">
<?php if ($emergencyBanner && $emergencyBanner['visible']) { ?>
    <?php get_component('c-emergency-banner', $emergencyBanner); ?>
<?php } ?>
<?php if ($fullWidthTopBanner && $fullWidthTopBanner['visible']) { ?>
    <?php get_component('c-full-width-banner', $fullWidthTopBanner); ?>
<?php } ?>
        <h1 class="o-title o-title--page"><?php echo $activeAgency['label'];?></h1>
        <?php get_component('c-home-page-primary'); ?>
        <?php get_component('c-home-page-secondary'); ?>
    </div>
<?php get_footer(); ?>
