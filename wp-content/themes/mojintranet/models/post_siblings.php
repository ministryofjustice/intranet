<?php if (!defined('ABSPATH')) die();

class Post_siblings_model extends MVC_model {

  /** Get a previous and next urls for a post object
   * @param {Array} $options Options and filters (see search model for details)
   * @return {Array} [
   *  prev_link - {String} previous post URL
   *  next_link - {String} next post URL
   * ]
   */
  public function get_post_sibling_links($options = []) {
    global $post;

    $post = get_post($options['post_id']);

    $args = [
      // Paging
      'nopaging' => false,
      'offset' => 0,
      'posts_per_page' => 1,
      // Filters
      'post_type' => $post->post_type,
      'tax_query' => $options['tax_query'],
      'orderby' => 'date',
      'post__not_in' => [$post->ID]
    ];

    $before_args = $args;
    $before_args['order'] = 'DESC';
    $before_args['date_query'] = [
      [
        'before' => $post->post_date,
        'inclusive' => true
      ]
    ];

    $after_args = $args;
    $after_args['order'] = 'ASC';
    $after_args['date_query'] = [
      [
        'after' => $post->post_date,
        'inclusive' => true
      ]
    ];

    $before_post = get_array_value(get_posts($before_args), 0, []);
    $after_post = get_array_value(get_posts($after_args), 0, []);

    return [
      'prev_link' => is_object($before_post) ? get_permalink($before_post->ID) : '',
      'next_link' => is_object($after_post) ? get_permalink($after_post->ID) : '',
    ];
  }
}
