<?php

$post_id = get_the_ID();

// Beware: HMCTS editors use the tabbed template to display regions
// ~ support isolated long-tail, tabbed region pages, without parents
$pre_breadcrumb_path = '';
if (has_post_parent($post_id)) {
    // Get the correct region name
    $region_id = get_the_terms($post_id, 'region');

    // Loop through using the region id and get current region name
    $current_region = '';
    if ($region_id) :
        foreach ($region_id as $region) :
            $current_region = $region->name;
        endforeach;
    endif;

    $current_region_url_formatted = sanitize_text_field(
        strtolower(
            str_replace([' ', '&amp;', '&'], ['-', 'and'], $current_region)
        )
    );

    $current_region_name_formatted = sanitize_text_field(ucwords($current_region));

    $pre_breadcrumb_path = '<li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated">
        <a href="/regional-pages/' . $current_region_url_formatted . '">
            <span>' . $current_region_name_formatted . '</span>
        </a>
    </li>';
}

?>

<!-- c-breadcrumbs (view-region-single) starts here -->
<section class="c-breadcrumbs">
    <ol class="c-breadcrumbs__list">
        <li class="c-breadcrumbs__list-item">
            <a title="Go home" href="<?= get_home_url() ?>" class="home">
                <span>Home</span>
            </a>
        </li>
        <li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated">
            <a href="/regional-pages/">
                <span>Regions</span>
            </a>
        </li>
        <?= $pre_breadcrumb_path ?>
        <li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated">
            <span><?php the_title(); ?></span>
        </li>
    </ol>
</section>
<!-- c-breadcrumbs (view-region-single) ends here -->
