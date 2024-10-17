<?php

use MOJ\Intranet\Agency;

// Catch the post type that has been selected in the search field so it can be saved when the search results are loaded.
$selected_value = (isset($_GET['post_types'])) ? $selected_value = $_GET['post_types'] : '';

$any = $selected_value === 'any';
$page = $selected_value === 'page';
$document = $selected_value === 'document';
$news = $selected_value === 'news';
$post = $selected_value === 'post';
$event = $selected_value === 'event';

$prefix = 'srf';
$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
?>
<!-- c-search-results-filter starts here -->
<section class="c-search-results-filter c-content-filter">
    <!-- p tag below to prevent box collapse whe position: fixed applied to form-->
    <p style="height:1px;padding:0 1px 0;margin:0 1px;"></p>
    <form action="<?= esc_url(home_url('/')) ?>" method="get" id="<?= $prefix ?>" class="u-wrapper" id="searchform">
        <p>You are searching across <strong><?php esc_attr_e($activeAgency['abbreviation']); ?></strong>.
        To search another agency use <a href="/agency-switcher">agency switcher</a>.</p>
        <?php
        $placeholder = 'Search' . $activeAgency['abbreviation'] . 'intranet';
        $keyword_query = get_search_query();

        // Keyword input field
        form_builder('text', '', false, 's', null, $keyword_query, $placeholder, null, false, null, null);

        echo '<input type="submit" value="Search" id="ff_button_submit"/><br><br><br><hr><br>';

        form_builder(
            'radio-group',
            '',
            'Filter by',
            'post_types',
            null,
            $selected_value,
            null,
            'js-radios-onChange',
            false,
            null,
            [
                ['All', 'any', $any],
                ['Pages', 'page', $page],
                ['Documents &amp; forms', 'document', $document],
                ['News', 'news', $news],
                ['Blogs', 'post', $post],
                ['Events', 'event', $event]
            ]
        );

        ?>
    </form>
</section>
<!-- c-search-results-filter ends here -->
