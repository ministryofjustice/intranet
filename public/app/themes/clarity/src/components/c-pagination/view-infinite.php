<?php

/**
 * Pagination for infinite scroll
 * 
 * This file should be used by calling get_template_part() and passing the total_pages and page arguments.
 * e.g. get_template_part('src/components/c-pagination/view-infinite', null, ['total_pages' => $query->max_num_pages, 'page' => 1]);
 * 
 * @package Clarity
 */

defined('ABSPATH') || exit;

if (empty($args['total_pages']) || empty($args['page'])) {
    return;
}

?>


<nav class="c-pagination" role="navigation" aria-label="Pagination Navigation">

    <?php if ($args['total_pages'] > $args['page']) : ?>

        <button class="more-btn" data-page="<?= $args['page'] + 1; ?>" data-date="">
            <span class="c-pagination__main ">
                <span class="u-icon u-icon--circle-down"></span> 
                Load Next 10 Results
            </span>
            <span class="c-pagination__count"> <?= $args['page']; ?> of <?= $args['total_pages']; ?></span>
        </button>

    <?php else : ?>

        <button class="more-btn" data-date="">
            <span class="c-pagination__main ">No Results Found</span>
            <span class="c-pagination__count"> 0 of <?= $args['total_pages']; ?></span>
        </button>

    <? endif; ?>
</nav>
