<?php if (!defined('ABSPATH')) die();

class Taxonomy_model extends MVC_model {
  function get($options = []) {
    $options = $this->_normalise_options($options);
    $clean_terms = [];

    $terms = get_terms($options['taxonomy'], [
      'hide_empty' => $options['hide_empty']
    ]);

    if (isset($options['agency'])) {
      $agency = get_term_by('slug', $options['agency'], 'agency');
      $agency_id = $agency->term_id;

      foreach ($terms as $index => $term) {
        $term_agencies = get_field('term_used_by', $options['taxonomy'] . '_' . $term->term_id);
        if (!in_array($agency_id, $term_agencies)) {
          unset($terms[$index]);
        }
      }
    }
    else {
      foreach ($terms as $index => $term) {
        $term_agencies = get_field('term_used_by', $options['taxonomy'] . '_' . $term->term_id);
        $terms[$index]->agencies = [];

        foreach ($term_agencies as $agency_id) {
          $agency_term = get_term($agency_id, 'agency');
          $terms[$index]->agencies[] = $agency_term->slug;
        }
      }
    }

    foreach ($terms as $term) {
      $clean_terms[] = $this->_format_row($term);
    }

    return $clean_terms;
  }

  private function _normalise_options($options) {
    $options['taxonomy'] = (string) $options['taxonomy'] ?: '';
    $options['hide_empty'] = (boolean) isset($options['hide_empty']) ? $options['hide_empty']: true;

    return $options;
  }

  private function _format_row($term) {
    return [
      'id' => $term->term_id,
      'name' => $term->name,
      'slug' => $term->slug,
      'agencies' => $term->agencies
    ];
  }
}
