<?php
/*
* Generates theme's standard main breadcrumb.
* Edge cases - not for pages that directly follow
* hierarchy from homepage (news, event, blog singles)
*/

function get_breadcrumb()
{

    global $post;

    $trail      = '';
    $page_title = get_the_title($post->ID);

    if ($post->post_parent) {
        $parent_id = $post->post_parent;

        while ($parent_id) {
            $page          = get_page($parent_id);
            $breadcrumbs[] = '<li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated"><a href="' . get_the_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
            $parent_id     = $page->post_parent;
        }

        $breadcrumbs = array_reverse($breadcrumbs);
        foreach ($breadcrumbs as $crumb) {
            $trail .= $crumb;
        }
    }

    $trail .= $page_title;

    return $trail;
}
?>

  <!-- c-breadcrumbs starts here -->
  <section class="c-breadcrumbs">
    <ol class="c-breadcrumbs__list">
      <li class="c-breadcrumbs__list-item">
        <a title="Go home." href="<?php echo get_home_url(); ?>" class="home">
          <span>Home</span>
        </a>
      </li>  
      <li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated">
        <a href="/regional-pages/">
          <span>Regions</span>
        </a>
      </li>
      <li class="c-breadcrumbs__list-item c-breadcrumbs__list-item--separated">
        <span><?php echo get_breadcrumb(); ?></span>
      </li>
    </ol>
  </section>
  <!-- c-breadcrumbs ends here -->
