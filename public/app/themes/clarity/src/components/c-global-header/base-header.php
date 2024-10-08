<?php
use MOJ\Intranet\Agency;

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Clarity
 */

$oAgency      = new Agency();
$activeAgency = $oAgency->getCurrentAgency() ? $oAgency->getCurrentAgency() : 'hq';

// define the title
$header_title = get_the_title();

// Search page?
if (is_search()) {
    $header_title = 'Search: ' . get_search_query();

    $query_post_type = sanitize_text_field($_GET['post_types'] ?? null);
    if ($query_post_type) {
        $header_title .= ' (' . $query_post_type . ')';
    }
}

?><!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
    <meta name="theme-color" content="<?= $agency_colour ?>"/>
    <meta name="agency" content="<?= $activeAgency['label'] ?>"/>

    <title><?= $header_title ?><?= ' - ' . $activeAgency['label'] . ' Intranet'; ?></title>

  <link rel="icon" sizes="180x180" href="<?= get_stylesheet_directory_uri() ?>/dist/images/icons/apple-touch-icon-180x180.png">
  <link rel="shortcut icon" href="<?= get_stylesheet_directory_uri() ?>/dist/images/icons/favicon.ico" type="image/x-icon" />
  <link rel="apple-touch-icon" href="<?= get_stylesheet_directory_uri(); ?>/dist/images/icons/apple-touch-icon.png" />
  <link rel="apple-touch-icon" sizes="57x57" href="<?= get_stylesheet_directory_uri() ?>/dist/images/icons/apple-touch-icon-57x57.png" />
  <link rel="apple-touch-icon" sizes="72x72" href="<?= get_stylesheet_directory_uri() ?>/dist/images/icons/apple-touch-icon-72x72.png" />
  <link rel="apple-touch-icon" sizes="76x76" href="<?= get_stylesheet_directory_uri() ?>/dist/images/icons/apple-touch-icon-76x76.png" />
  <link rel="apple-touch-icon" sizes="114x114" href="<?= get_stylesheet_directory_uri() ?>/dist/images/icons/apple-touch-icon-114x114.png" />
  <link rel="apple-touch-icon" sizes="120x120" href="<?= get_stylesheet_directory_uri() ?>/dist/images/icons/apple-touch-icon-120x120.png" />
  <link rel="apple-touch-icon" sizes="144x144" href="<?= get_stylesheet_directory_uri() ?>/dist/images/icons/apple-touch-icon-144x144.png" />
  <link rel="apple-touch-icon" sizes="152x152" href="<?= get_stylesheet_directory_uri() ?>/dist/images/icons/apple-touch-icon-152x152.png" />
  <link rel="apple-touch-icon" sizes="180x180" href="<?= get_stylesheet_directory_uri() ?>/dist/images/icons/apple-touch-icon-180x180.png" />
    <?php
    wp_head() ?>

   <script defer>
      document.addEventListener('DOMContentLoaded', function() {
        // Ensure the correct agency cookie gets picked up
        var agency_cookie = ('; ' + document.cookie).split('; dw_agency=').pop().split(';').shift();
        if (agency_cookie !== null && agency_cookie !== '<?= $agency_shortcode ?>') { window.location.reload() }
      });
    </script>
</head>
<?php
if (! defined('GT_CODE')) {
    define('GT_CODE', 'GTM-P545JM');
}
?>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?= GT_CODE ?>"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?= GT_CODE ?>');</script>
<!-- End Google Tag Manager -->
<body
<?php
/**
 * Adds agency specific classes to the page.
 */
  $agency_class      = 'agency-' . $agency_shortcode;
  body_class($class = $agency_class);
?>
>
<a class="u-skip-link" href="#maincontent">Skip to main content</a>

<?php
get_template_part('src/components/c-header-container/view');
get_template_part('src/components/c-phase-banner/view');
