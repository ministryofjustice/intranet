<?php
use MOJ\Intranet\Agency;

function get_pagination($type, $category_id = false, $post_per_page = 10)
{
    $oAgency      = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();

    $args = [
      'numberposts' => $post_per_page,
      'post_type' => $type === 'posts' ? 'post' : $type,
      'post_status' => 'publish',
      'tax_query' => [
        'relation' => 'AND',
        [
          'taxonomy' => 'agency',
          'field' => 'term_id',
          'terms' => $activeAgency['wp_tag_id']
        ],
        // If the category_id is set add it to the taxonomy query
        ...($category_id ? [
          'taxonomy' => 'news_category',
          'field' => 'category_id',
          'terms' => $category_id,
        ] : []),
      ]
    ];

    $query = new WP_Query($args);
    $pagetotal = $query->max_num_pages;

    ?>
        <div id="load_more"></div>
        <nav class="c-pagination" role="navigation" aria-label="Pagination Navigation">
        <?php
        if ($pagetotal > 0) {
            ?>
            <button class="more-btn" data-page="2" data-date="">
            <span class="c-pagination__main "><span class="u-icon u-icon--circle-down"></span> Load Next 10 Results</span><span class="c-pagination__count"> 1 of <?php echo $pagetotal; ?></span>
            </button>
            
            <?php
        } else {
            ?>
        <button class="more-btn" data-date="">
            <span class="c-pagination__main ">No Results Found</span>
            <span class="c-pagination__count"> 0 of <?php echo $pagetotal; ?></span>
            </button>
        </nav>
            <?php
        }
        wp_reset_postdata();
}
