<?php

global $post;
/* Get an array of Ancestors and Parents if they exist */
$parents = get_post_ancestors( $post->ID );
/* Get the top Level page->ID count base 1, array base 0 so -1 */
$id = ($parents) ? $parents[count($parents)-1]: $post->ID;

$parent = get_post( $id );
$parent_name = $parent->post_title;
$parent_link = get_the_permalink($parent->ID);

?>
<section class="c-breadcrumbs">
  <a title="Go to MoJ Intranet." href="<?php echo get_home_url(); ?>" class="home">
    <span>MoJ Intranet</span>
  </a>
  <span> &gt; </span>

  <a href="<?php echo $parent_link; ?>">
    <span><?php echo $parent_name; ?></span>
  </a>

  <span> &gt; </span>

  <span><?php the_title(); ?></span>
</section>
