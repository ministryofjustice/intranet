<?php

/**
 * The template for displaying Search Results pages.
 *
 * Template name: Search results
 */

Debug::full($_GET);
Debug::full(get_query_var('s'));


class Page_search_results extends MVC_controller {
  function main() {
    get_header();
    $this->view('shared/breadcrumbs');
    $this->view('pages/search_results/main', $this->get_data());
    get_footer();
  }

  private function get_data() {
    $results = $this->get_results($this->category_slug, $this->keywords);

    $data = array(
      'post_count' => $results->post_count,
      'total_count' => (int) $results->found_posts,
      'posts' => array()
    );

    //Debug::full($results, 2);

    while($results->have_posts()) {
      $results->the_post();

      $post_id = $post->ID;
      $article_date = get_the_date();

      $data['posts'][] = array(
        'title' => get_the_title(),
        'thumbnail' => wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'newshead'),
        'excerpt' => get_the_excerpt(),
        'raw_date' => $article_date,
        'human_date' => date("j F Y", strtotime($article_date)),
        'category_name' => 'Category',
        'subcategory_name' => 'HQ'
      );
    }

    //Debug::brief($data);

    return $data;
  }

  private function get_results($category_slug, $keywords) {
    $results = new WP_Query(array(
      'posts_per_page' => 10,
      'paged' => 1, //page number
      'post_type' => array('blog', 'event', 'news', 'post', 'page'),
      'category_name' => $category_slug,
      's' => $keywords
    ));

    return $results;
  }
}

new Page_search_results();
