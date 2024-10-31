<?php

/**
 * Individual blog feed list item - AJAX template.
 * 
 * The template is used for search results loaded via AJAX.
 * Class names and html structure matches the view-news-feed.php component.
 *
 * @package Clarity
 */

defined('ABSPATH') || exit;

?>

<script type="text/template" data-template="view-post-feed">

    <article class="c-article-item  c-article-item--blog js-article-item" data-type="">

        ${?post_thumbnail}
            <a tabindex="-1" aria-hidden="true" href="${permalink}" class="thumbnail">
                <img src="${post_thumbnail}" alt="${post_thumbnail_alt}">
            </a>
        ${/?post_thumbnail}

        <div class="content">
            <h1>
                <a href="${permalink}">${post_title}</a>
            </h1>

            <div class="meta">
                <span class="c-article-item__dateline">
                    ${?author_display_name}
                        By <strong>${author_display_name}</strong> |
                    ${/?author_display_name}
                    <span class="c-article-item__dateline__date">${post_date_formatted}</span>
                </span>
            </div>

            <div class="c-article-excerpt">${post_excerpt_formatted}</div>
        </div>
    </article>

</script>
