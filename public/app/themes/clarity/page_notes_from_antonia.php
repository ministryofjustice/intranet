<?php
/*
* Template Name: Notes from Antonia
*/
get_header();
get_template_part('src/components/c-campaign-colour/view');

// features
$stv_pre = 'scroll_to_view_group_';
$img_pre = 'image_load_manage_group_';
$page_settings = [
    'loading_message' =>  get_post_meta($post->ID, 'loading_message_text', true),
    'scroll_to_view' => [
        'active' => get_post_meta($post->ID, $stv_pre . 'scroll_to_view_active', true)
    ],
    'image_load' => [
        'active' => get_post_meta($post->ID, $img_pre . 'image_load_active', true)
    ],
    'year_filter' => isset($_GET['year-filter']) && (int)$_GET['year-filter'] > 2016 && (int)$_GET['year-filter'] < 2048  ? (int)$_GET['year-filter'] : null
];

if ($page_settings['scroll_to_view']['active']) {
    $stv_pre .= 'stv_settings_';
    $page_settings['scroll_to_view']['delay'] = get_post_meta($post->ID, $stv_pre . 'scroll_to_view', true);
    $page_settings['scroll_to_view']['speed'] = get_post_meta($post->ID, $stv_pre . 'scroll_to_view_speed', true);
}

if ($page_settings['image_load']['active']) {
    $img_pre .= 'image_load_settings_';
    $page_settings['image_load']['fade_in'] = get_post_meta($post->ID, $img_pre . 'image_loaded_fade_in', true);
}

?>
    <script>
        /**
         * Makes available user settings
         * @type {{image_loaded_fade_in: *, scroll_to_view: *}}
         */
        window.mojAjax.page_settings = {
            loading_message: '<?= $page_settings['loading_message'] ?>',
            scroll_to_view: {
                active: <?= $page_settings['scroll_to_view']['active'] ?>,
                delay: <?= $page_settings['scroll_to_view']['delay'] ?? 800 ?>,
                speed: <?= $page_settings['scroll_to_view']['speed'] ?? 600 ?>
            },
            image_load: {
                active: <?= $page_settings['image_load']['active'] ?>,
                fade_in: <?= $page_settings['image_load']['fade_in'] ?? 400 ?>
            }
        };
    </script>

    <main role="main" id="maincontent" class="u-wrapper l-main l-reverse-order t-default js-notes-from-antonia">

        <?php get_template_part('src/components/c-breadcrumbs/view') ?>

        <div role="status" class="l-primary">
            <?php get_template_part('src/components/c-campaign-banner/view') ?>
            <h1 class="o-title o-title--page"><?php the_title(); ?></h1>

            <section class="c-article-excerpt">
                <p><?= get_field('blog_roll_excerpt') ?></p>
            </section>

            <div class="template-container ">
                <?php get_template_part('src/components/c-rich-text-block/view'); ?>
            </div>

            <div id="content">
                <?php get_notes_api('note-from-antonia', $page_settings['year_filter']); ?>
            </div>
        </div>

        <section class="l-full-page">
            <?php
            get_template_part('src/components/c-last-updated/view');
            get_template_part('src/components/c-share-post/view');
            ?>
        </section>
    </main>

<?php

get_footer();
