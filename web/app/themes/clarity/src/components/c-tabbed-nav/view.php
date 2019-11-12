<!-- c-tabbed-nav starts here -->
  <?php
  if (have_rows('guidance_tabs')) :
      echo '<ul class="c-tabbed-content__nav" />';

      while (have_rows('guidance_tabs')) :
          the_row();

          $tab_count = count(get_field('guidance_tabs'));

          if ($tab_count > 1) :
              echo '<li>' . get_sub_field('tab_title') . '</li>';
          endif;
      endwhile;

        echo '</ul>';
  endif;
    ?>
<!-- c-tabbed-nav ends here -->
