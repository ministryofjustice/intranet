<?php 
  $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

  $args = array(
      'paged' => $paged,
      'posts_per_page' => 5,
      'post_type' => 'post',
  );
  $query = new WP_Query( $args );

  $prev_page_number = $paged-1;
  $next_page_number = $paged+1;

  $total_page_number = $query->max_num_pages;
?>

<!-- c-pagination starts here -->
<nav class="c-pagination" role="navigation" aria-label="Pagination Navigation">
  <?php 
    echo previous_posts_link( '<span class="c-pagination__main">Previous page</span><span class="c-pagination__count">'.$prev_page_number.' of '.$total_page_number.'</span>' );
    echo next_posts_link( '<span class="c-pagination__main">Next page</span><span class="c-pagination__count">'.$next_page_number.' of '.$total_page_number.'</span>',$total_page_number );
  ?>
</nav>
<!-- c-pagination ends here -->
