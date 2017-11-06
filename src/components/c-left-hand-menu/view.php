<!-- c-left-hand-menu starts here -->
<nav class="c-left-hand-menu js-left-hand-menu">
  <a href="" class="c-left-hand-menu__step_back">Change Directorate <<</a><br />
  <ul>
  <?php
    $parentID = wp_get_post_parent_id( $post_ID );
    $args = array(
      'parent'  => $parentID,
      'depth'     => 1,
      'post_type' => 'page',
      'post_status'=> 'publish'
  
    );
    $children  = get_pages( $args );

    foreach ($children as $child) {
      echo '<li><a href="' . get_permalink($child->ID) . '">';
        echo $child->post_title;
      echo '</a></li>';
    }
 
   ?>
  </ul>
</nav>
<!-- c-left-hand-menu ends here -->
