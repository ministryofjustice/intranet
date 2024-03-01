<?php

$parent_ID         = wp_get_post_parent_id($post->ID);
$current_post_type = get_post_type();


if ($current_post_type === 'regional_page') :
    // Arguments needed to be passed to wp_list_pages() when the child pages are a custom post type.
    $page_args = [
        'child_of'    => $post->ID,
        'title_li'    => 0,
        'post_type'   => 'regional_page',
        'post_status' => 'publish',
        'link_after'  => '<span class="dropdown"></span>',
        'order'       => 'ASC',
        'orderby'     => 'menu_order',
    ];
else :
        $page_args = [
            'child_of'    => $post->ID,
            'depth'       => 0,
            'exclude'     => $parent_ID,
            'title_li'    => 0,
            'post_status' => 'publish',
            'link_after'  => '<span class="dropdown"></span>',
            'order'       => 'ASC',
            'orderby'     => 'menu_order',
        ];
endif;


    $child_page_args = [
        'post_parent' => $post->ID,
        'post_type'   => 'any',
        'numberposts' => -1,
        'post_status' => 'publish',
    ];

    $child_pages = get_children($child_page_args);

    if ($child_pages) :
        ?>

<!-- c-left-hand-menu starts here -->
<nav class="c-left-hand-menu js-left-hand-menu">

  <div class="c-left-hand-menu__step_back">
        <?php echo get_the_title($post->ID); ?>
  </div>
  <ul><?php wp_list_pages($page_args); ?></ul>
</nav>
<!-- c-left-hand-menu ends here -->

        <?php
    endif;
