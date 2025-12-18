<?php
use MOJ\Intranet\Agency;

/**
 * The header for our theme
 *
 * This is the template that displays all the <head> section
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Clarity
 */

$oAgency      = new Agency();
$activeAgency = $oAgency->getCurrentAgency() ? $oAgency->getCurrentAgency() : 'hq';

?><!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <meta name="theme-color" content="<?php echo $agency_colour; ?>">
    <meta name="agency" content="<?php echo $activeAgency['label']; ?>">

    <title><?php echo single_post_title(); ?><?php echo ' - ' . $activeAgency['label'] . ' Intranet'; ?></title>

    <link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/icons/favicon-48x48.png" sizes="48x48" />
    <link rel="icon" type="image/svg+xml" href="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/icons/favicon.svg" />
    <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/icons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MoJ Intranet" />
    <link rel="manifest" href="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/icons/site.webmanifest" />
    <?php
    /**
     * wp_head() required WP function do not remove. Used by plugins to hook into and for theme development.
     */
    wp_head();
    ?>
    <script defer>
      function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0) == ' ') c = c.substring(1, c.length);
          if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
      }
      document.addEventListener('DOMContentLoaded', function() {
        <!-- Ensure the correct agency cookie gets picked up -->
        var agency_cookie = readCookie("dw_agency");
        if (agency_cookie !== null && agency_cookie !== '<?php echo $agency_shortcode; ?>') { window.location.reload(true); }
      });
    </script>
</head>
<body
    <?php
    /**
     * Adds agency specific classes to the page.
     */
    $agency_class      = 'agency-' . $agency_shortcode;
    body_class($class = $agency_class);
    ?>
>
<?php
if (! defined('GT_CODE')) {
    define('GT_CODE', 'GTM-P545JM');
}
?>
<!-- Google Tag Manager -->
<noscript>
    <iframe src="//www.googletagmanager.com/ns.html?id=<?php echo GT_CODE; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<script>
  (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','<?php echo GT_CODE; ?>');
</script>
<!-- End Google Tag Manager -->
<a class="u-skip-link" href="#maincontent">Skip to main content</a>

<?php
get_template_part('src/components/c-header-container/view');
get_template_part('src/components/c-phase-banner/view');
