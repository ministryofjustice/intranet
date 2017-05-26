<!-- c-left-hand-menu starts here -->
<nav class="c-left-hand-menu js-left-hand-menu">
  <a href="" class="c-left-hand-menu__step_back">Change Directorate</a>
  <ul>
  <?php
    wp_list_pages(array(
      'depth'       => 20,
      'title_li'    => '',
      'child_of'    => 290)
    );
  ?>
  </ul>
</nav>
<!-- c-left-hand-menu ends here -->
