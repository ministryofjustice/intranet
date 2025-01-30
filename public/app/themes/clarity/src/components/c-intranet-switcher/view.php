<?php

use MOJ\Intranet\Agency;

$oAgency = new Agency();
$activeAgencies = $oAgency->getList();
$current_intranet = get_intranet_code();

$referrer = (isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : parse_url(get_home_url()));
$url = get_home_url();

// Redirect back to the previous page after selecting an agency if this is their first visit
if (isset($_GET['send_back'])) {
    // Validate the URL to prevent open redirects
    $url = wp_validate_redirect($_GET['send_back'], get_home_url());
}

/**
 * Remove the query parameter so it isn't sent
 * twice/several times on repeated access to this page
 */
if (isset($referrer['query'])) {
    parse_str($referrer['query'], $output);

    if (isset($output['agency'])) {
        unset($output['agency']);
    }

    // Remove send_back as it's only needed to set the url
    if (isset($output['send_back'])) {
        unset($output['send_back']);
    }

    $previous_parameters = [];

    foreach ($output as $key => $value) {
        $previous_parameters[] = $key . '=' . $value;
    }

    $referrer['query'] = implode('&', $previous_parameters);

    if (trim($referrer['query']) != '') {
        $referrer['query'] = '&' . $referrer['query'];
    }
} else {
    $referrer['query'] = '';
}

// Temporarily filtering out JAC/PB until site is ready to go live
// 10th Jan 2024: Parole Board added to exclude list: https://dsdmoj.atlassian.net/jira/software/c/projects/CDPT/boards/1154?selectedIssue=CDPT-1170
$excluded = ['pb'];
$integrated = [];
$external = [];

$filteredAgencies = array_filter($activeAgencies, function($agency) use ($excluded) {
    return !in_array($agency['shortcode'], $excluded);
});

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
                            echo '<li class="c-intranet-switcher__switch c-intranet-switcher__switch--' . $agency_id . $extra_class . ' "><a href="'. $url .'?agency=' . $agency_id . $referrer['query'] . '">' . $agency['label'] . '</a></li>';
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
