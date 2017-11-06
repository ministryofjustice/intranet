<!-- c-left-hand-menu starts here -->
<nav class="c-left-hand-menu js-left-hand-menu">
  <a href="" class="c-left-hand-menu__step_back">Change Directorate <<</a><br />
  <ul>
  <?php
    wp_list_pages(
      [
      'depth'       => 1,
      'title_li'    => "",
      'child_of'    => 0,
      'post_type'    => 'page',
      'post_status'  => 'publish'
      ]
    );
  ?>
  </ul>
</nav>
<!-- c-left-hand-menu ends here -->
