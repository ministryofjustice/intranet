<?php

$enable_banner = get_field('enable_agency_wide_banner', 'option');
$banner_title = get_field('agency_wide_banner_title', 'option');
$banner_message = get_field('agency_wide_banner_message', 'option');
$banner_link = get_field('agency_wide_banner_link', 'option');

$banner_type_class = 'banner-type-notice';

$banner_type = get_field('agency_wide_banner_type', 'option');

if (!empty($banner_type)) {
    $banner_type_class = 'banner-type-' . $banner_type;
}


?>

<?php if ($enable_banner == true) { ?>
    <!-- c-agency-wide-banner starts here -->
    <section class="c-agency-wide-banner <?php echo $banner_type_class; ?>">
        <?php if (!empty($banner_title)) { ?>
            <h2><?php echo $banner_title; ?></h2>
        <?php } ?>

        <?php if (!empty($banner_message)) { ?>
            <div class="c-agency-wide-banner__content">
                <?php echo $banner_message; ?>
            </div>
        <?php } ?>

        <?php if (!empty($banner_link)) {?>
            <div class="banner-link">
                <a href="<?php echo $banner_link['url']; ?>" target="<?php echo $banner_link['target']; ?>"><?php echo $banner_link['title']; ?></a>
            </div>
        <?php } ?>

    </section>
    <!-- c-agency-wide-banner ends here -->
<?php } ?>