<?php

$enable_banner = get_field('enable_agency_wide_banner', 'option');
$banner_text = get_field('agency_wide_banner_text', 'option');
$banner_link = get_field('agency_wide_banner_link', 'option');
?>

<?php if ($enable_banner == true) { ?>
    <!-- c-agency-wide-banner starts here -->
    <section class="c-agency-wide-banner">
        <?php if(!empty($banner_text)){ ?>
            <h2><?php echo $banner_text; ?></h2>
        <?php } ?>

        <?php if(!empty($banner_link)){?>
            <div class="banner-link">
                <a href="<?php echo $banner_link['url']; ?>" target="<?php echo $banner_link['target']; ?>"><?php echo $banner_link['title']; ?></a>
            </div>
        <?php } ?>

    </section>
    <!-- c-agency-wide-banner ends here -->
<?php } ?>