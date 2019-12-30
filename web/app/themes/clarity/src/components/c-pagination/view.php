<?php
echo '<div class="c-pagination__main">';
echo '<hr>';
the_posts_pagination(
    [
        'prev_text'          => '<span class="screen-reader-text">' . __('Previous', 'clarity') . '</span>',
        'next_text'          => '<span class="screen-reader-text more-btn--next">' . __('Next', 'clarity') . '</span>',
        'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'clarity') . ' </span>',
    ]
);
echo '</div>';
