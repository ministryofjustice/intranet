<?php

if (!empty($latestNews) )
{
    ?>
    <div class="c-news-list">
        <?php
        foreach ($latestNews['results'] as $postItem) {
            get_component('c-article-item', $postItem, 'show_date');
        }
        ?>
    </div>
    <?php
}
