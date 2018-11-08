<?php
namespace MOJ\Intranet;

if (!defined('ABSPATH')) die();

class Events  {

    var $searchHelper;

    function __construct()
    {
        $this->searchHelper = new HelperSearch();
    }


    /** Get a list of events
    * @param {Array} $options Options and filters (see search model for details)
    * @return {Array} Formatted and sanitized results
    */
    public function getEvents($options = array()) {
        $options['search_orderby'] = array(
          '_event-start-date' => 'ASC',
          '_event-end-date' => 'ASC',
          'title' => 'ASC',
        );
        $options['meta_fields'] = array('_event-start-date', '_event-end-date');
        $options['post_type'] = 'event';
        $options['date'] = get_array_value($options, 'date', 'today');

        if (!empty($options['tax_query']) && !has_taxonomy($options['tax_query'], 'region')) {
          $term_slugs = get_term_slugs('region');

          $options['tax_query'][] = [
            'taxonomy' => 'region',
            'field' => 'slug',
            'terms' => $term_slugs,
            'operator' => 'NOT IN'
          ];
        }

        $data = $this->searchHelper->get_raw($options);
        $data = $this->format_data($data);

        return $data;
    }

    /** Format and trim the raw results
    * @param {Object} $data Raw results object
    * @return {Array} Formatted results
    */
    private function format_data($data) {
        $results = array ();

        foreach($data['raw']->posts as $post) {
            $results[] = $this->format_row($post);
        }

        unset($data['raw']);

        return $results;
    }

    /** Format a single results row
    * @param {Object} $post Post object
    * @return {Array} Formatted and trimmed post
    */
    private function format_row($post) {
        $id = $post->ID;

        $start_date = get_post_meta($id, '_event-start-date', true);
        $end_date = get_post_meta($id, '_event-end-date', true);

        return array(
              'id' => $id,
              'title' => (string) get_the_title($id),
              'url' => (string) get_the_permalink($id),
              'slug' => (string) $post->post_name,
              'location' => (string) get_post_meta($id, '_event-location', true),
              'description' => (string) get_the_content_by_id($id),
              'start_date' => (string) $start_date,
              'start_time' => (string) get_post_meta($id, '_event-start-time', true),
              'end_date' => (string) $end_date,
              'end_time' => (string) get_post_meta($id, '_event-end-time', true),
              'all_day' =>  get_post_meta($id, '_event-allday', true) == true,
              'multiday' => (string) $start_date !== $end_date
        );
    }
}
