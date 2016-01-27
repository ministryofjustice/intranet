<?php if (!defined('ABSPATH')) die();

class News_model extends MVC_model {
  public function get_list($options = array()) {
    $options['search_order'] = 'DESC';
    $options['search_orderby'] = 'date';
    $options['post_type'] = 'news';

    $data = $this->model->search->get_raw($options);
    $data = $this->format_data($data);

    return $data;
  }

  private function format_data($data) {
    $data['results'] = array();

    foreach($data['raw']->posts as $post) {
      $data['results'][] = $this->format_row($post);
    }

    unset($data['raw']);

    return $data;
  }

  private function format_row($post) {
    $id = $post->ID;

    $post_object = get_post($id);

    $thumbnail_id = get_post_thumbnail_id($id);
    $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'thumbnail');
    $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

    return array(
      'id' => $id,
      'title' => (string) get_the_title($id),
      'url' => (string) get_the_permalink($id),
      'slug' => (string) $post->post_name,
      'excerpt' => (string) $post->post_excerpt,
      'thumbnail_url' => (string) $thumbnail[0],
      'thumbnail_alt_text' => (string) $alt_text,
      'timestamp' => (string) get_the_time('Y-m-d H:i:s', $id)
    );
  }
}
