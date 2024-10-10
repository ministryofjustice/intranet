<?php

/**
 * Pagination template.
 * 
 * The template is used for search results loaded via AJAX.
 * Class names and html structure matches the pagination.php file.
 *
 * @package Clarity
 */

?>

<?php defined('ABSPATH') || exit; ?>

<script type="text/template" data-template="pagination">
    <button class="more-btn" ${disabled}>
        <span class="c-pagination__main "><span class="u-icon u-icon--circle-down"></span> ${title}</span>
        <span class="c-pagination__count"> ${currentPageFormatted} of ${totalPages} </span>
    </button>
</script>
