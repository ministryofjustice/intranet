<?php

$args = [
    'post_type' => 'condolences',
    'posts_per_page' => -1,
    'post_status' => 'publish'
];

$query = new WP_Query($args);

if ($query->have_posts()) {
    ?>
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
