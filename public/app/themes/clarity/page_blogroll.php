<?php
/*
* Template Name: Blogroll (Notes from Perm. Sec.)
*/
get_header();
get_template_part('src/components/c-campaign-colour/view');

// features
$stv_pre = 'scroll_to_view_group_';
$img_pre = 'image_load_manage_group_';
$page_settings = [
    'post_type' => get_post_meta($post->ID, 'content_post_type', true),
    'loading_message' =>  get_post_meta($post->ID, 'loading_message_text', true),
    'scroll_to_view' => [
        'active' => get_post_meta($post->ID, $stv_pre . 'scroll_to_view_active', true)
    ],
    'image_load' => [
        'active' => get_post_meta($post->ID, $img_pre . 'image_load_active', true)
    ],
    'is_archived' => get_post_meta($post->ID, 'is_archived', true),
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

if($page_settings['is_archived']) {
    $page_settings['archive_redirect'] = get_post_meta($post->ID, 'archive_redirect', true);
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

        <?php if($page_settings['is_archived']): ?>
            <div class="c-moj-banner c-moj-banner--warning">
                <div class="c-moj-banner__message">
                    <h2 class="o-title">This page is archived</h2>
                    <p>This page is archived and no longer updated. Agency Admins have access to this page,
                        users with other accounts, and visitors without an account will be redirected to:
                        <a href="<?php the_permalink($page_settings['archive_redirect']); ?>">
                            <?php echo get_the_title($page_settings['archive_redirect']); ?>
                        </a>
                    </p>
                </div>
            </div>
        <?php endif; ?>

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
                <?php get_blogroll_posts_api($page_settings['post_type']); ?>
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
