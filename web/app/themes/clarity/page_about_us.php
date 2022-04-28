<?php
use MOJ\Intranet\Agency;

/*
* Template Name: About us
*/
if (!defined('ABSPATH')) {
    die();
}
get_header();
$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
$id = get_the_ID();
$hq_id = 116;
?>
    <main role="main" id="maincontent" class="u-wrapper l-main t-about-us">

        <?php
            // get agency specifc about us
            $enable_agency = get_field('enable_agency_about_us');
            $hq_agency = ($activeAgency['shortcode'] == 'hq');
            if ($enable_agency && !$hq_agency) {
                echo '<h2 class="o-title o-title--page">About '.$activeAgency['label'].'</h2>';
                $agency_specific_description = get_field('agency_specific_description');
                if ($agency_specific_description) {
                    echo '<p>' .$agency_specific_description. '</p>';
                }
                get_aboutus_list($id);
                echo '<div class="dividers"></div>';
            }

            // get moj about us
            $enable_aboutmoj = get_field('enable_moj_about_us');
            if ($enable_aboutmoj) {
                echo '<h2 class="o-title o-title--page" id="title-section">Ministry of Justice</h2>';
                $moj_description = get_field('moj_description');
                if ($moj_description) {
                    echo '<p>' .$moj_description. '</p>';
                }
                get_aboutus_list($hq_id);
            }
        ?>
    </main>
<?php
get_footer();
