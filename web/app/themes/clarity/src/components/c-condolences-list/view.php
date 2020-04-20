<?php

$args = [
    'post_type' => 'condolences',
    'posts_per_page' => -1,
    'post_status' => 'publish'
];

$query = new WP_Query($args);

if ($query->have_posts()) {
    ?>
    <div class="view-options">
        View:
        <button class="view-by-list current">List</button> |
        <button class="view-by-grid">Grid</button>
    </div>
    <section class="c-condolences-list">
        <div>
            <?php
            while ($query->have_posts()) {
                $query->the_post(); ?>
                <?php get_template_part('src/components/c-condolence/view', 'list'); ?>
                <?php
            }
            wp_reset_query();
            ?>
        </div>
    </section>
    <?php
}
?>
