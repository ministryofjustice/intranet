<?php
/*
* Generates theme's standard main breadcrumb.
* Edge cases - not for pages that directly follow
* hierarchy from homepage (news, event, blog singles)
*/

function get_breadcrumb(): string
{
    global $post;

    $trail = '';
    $breadcrumbs = [];

    if ($post->post_parent) {
        $parent_id = $post->post_parent;

        while ($parent_id) {
            $page = get_post($parent_id);
            $breadcrumbs[] = '<li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated">
                <a href="' . get_the_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>
            </li>';

            $parent_id = $page->post_parent;
        }

        $trail = implode('', array_reverse($breadcrumbs));
    }

    return $trail . '<li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated">' . get_the_title($post->ID) . "</li>";
}

?>

<!-- c-breadcrumbs starts here -->
<section class="c-breadcrumbs">
    <ol class="c-breadcrumbs__list">
        <li class="c-breadcrumbs__list-item">
            <a title="Go home." href="<?= get_home_url() ?>" class="home">
                <span>Home</span>
            </a>
        </li>
        <?= get_breadcrumb() ?>
    </ol>
</section>
<!-- c-breadcrumbs ends here -->
