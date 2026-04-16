<?php

/**
 * Individual people-update (People Promise update) item - AJAX template.
 * 
 * The template is used for filter results loaded via AJAX.
 * Class names and html structure matches the view.php component.
 *
 * @package Clarity
 */

defined('ABSPATH') || exit;

?>

<script type="text/template" data-template="c-people-update-article-item">

    <article class="c-people-update-article-item">

        <div href="${permalink}" class="c-people-update-article-item--thumbnail">
            ${post_thumbnail}
            ${?pillar_name}
                <span class="c-people-update-article-item__pillar c-people-update-article-item__pillar--${pillar_slug}">
                    ${pillar_name}
                </span>
            ${/?pillar_name}
        </div>

        <div href="${permalink}" class="c-people-update-article-item--text">
            <h1 class="o-title">${post_title}</h1>
            ${post_content}
        </div>

    </article>

</script>
