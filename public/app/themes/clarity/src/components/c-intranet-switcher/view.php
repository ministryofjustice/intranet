<?php

use MOJ\Intranet\Agency;

$oAgency = new Agency();
$activeAgencies = $oAgency->getList();
$current_intranet = get_intranet_code();

$url = get_home_url();

// Redirect back to the previous page after selecting an agency if this is their first visit
if (isset($_GET['send_back'])) {
    // Validate the URL to prevent open redirects
    $url = wp_validate_redirect($_GET['send_back'], get_home_url());
}

// Temporarily filtering out JAC/PB until site is ready to go live
// 10th Jan 2024: Parole Board added to exclude list: https://dsdmoj.atlassian.net/jira/software/c/projects/CDPT/boards/1154?selectedIssue=CDPT-1170
$excluded = ['pb'];
$integrated = [];
$external = [];

// Remove the excluded agencies
$filteredAgencies = array_filter($activeAgencies, function($agency) use ($excluded) {
    return !in_array($agency['shortcode'], $excluded);
});

// Alphabetically sort the agencies
uasort($filteredAgencies, function($a, $b) {
    return strcmp($a['label'], $b['label']);
});

// Split the agencies into integrated and external
array_map(function($key, array $agency) use (&$integrated, &$external) {
    $shortcode = $agency['shortcode'];
    if ($agency['is_integrated']) {
        $integrated[$shortcode] = $agency;
    } else {
        $homepage = array_filter($agency['links'], function ($link) {
            return $link['main'];
        });

        $external[$shortcode] = $homepage ? [...$agency, 'homepage' => $homepage[0]['url']] : $agency;
    }
}, array_keys($filteredAgencies), $filteredAgencies);

/**
 * Displaying agencies by rows and adding agency
 * specific classes to those agencies listed.
 */
?>
<div class="c-intranet-switcher">
    <nav class="c-intranet-switcher__nav">
        <ul class="c-intranet-switcher__list">
            <li class="c-intranet-switcher__list-element">
                <ul class="c-intranet-switcher__section">
                    <?php
                        foreach ($integrated as $agency_id => $agency) {
                            if ($current_intranet == $agency_id) {
                                $extra_class = ' u-active';
                            } else {
                                $extra_class = '';
                            }
                            printf(
                                '<li class="c-intranet-switcher__switch c-intranet-switcher__switch--%s %s"><a href="%s?agency=%s">%s</a></li>',
                                esc_attr($agency_id),
                                esc_attr($extra_class),
                                esc_url($url),
                                esc_attr($agency_id),
                                esc_html($agency['label'])
                            );
                        }
                    ?>
                </ul>
            </li>
            <?php
            if (!empty($external)) {
                ?>
                <li class="c-intranet-switcher__list-element">
                    <h2 class="o-title o-title--subtitle c-intranet-switcher__heading">Other intranets</h2>
                    <p>These agencies have their own separate intranet sites.</p>
                    <ul class="c-intranet-switcher__section">
                        <?php
                        foreach ($external as $agency_id => $agency) {
                            echo '<li class="c-intranet-switcher__switch c-intranet-switcher__switch--' . $agency_id . ' "><a href="'. $agency['homepage']. '">' . $agency['label'] . '</a></li>';
                        }
                        ?>
                    </ul>
                </li>
                <?php
            }
            ?>
        </ul>
    </nav>
</div>
