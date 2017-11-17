<?php
namespace MOJ\Intranet;

if (!defined('ABSPATH')) die();

class Posts {

    var $searchHelper;

    function __construct()
    {
        $this->searchHelper = new HelperSearch();
    }


    /** Get a list of posts
     * @param {Array} $options Options and filters (see search model for details)
     * @return {Array} Formatted and sanitized results
     */
    public function getPosts($options = array(), $exclude_featured = false) {
        $options['search_order'] = 'DESC';
        $options['search_orderby'] = 'date';
        $options['post_type'] = 'post';

        if ($exclude_featured) {
            $options['post__not_in'] = News::getFeaturedIds(get_intranet_code());
            $options['search_orderby'] = 'post__in';
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
        $data['results'] = array();

        foreach($data['raw']->posts as $post) {
            $data['results'][] = $this->format_row($post);
        }

        unset($data['raw']);

        return $data;
    }

    /** Format a single results row
     * @param {Object} $post Post object
     * @return {Array} Formatted and trimmed post
     */
    private function format_row($post) {
        $id = $post->ID;
        setup_postdata(get_post($id));

        $thumbnail_id = get_post_thumbnail_id($id);
        $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'thumbnail');
        $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
        $authors = dw_get_author_info($id);

        return array(
            'id' => $id,
            'title' => (string) get_the_title($id),
            'url' => (string) get_the_permalink($id),
            'slug' => (string) $post->post_name,
            'excerpt' => (string) get_the_excerpt_by_id($id),
            'thumbnail_url' => (string) $thumbnail[0],
            'thumbnail_alt_text' => (string) $alt_text,
            'timestamp' => (string) get_the_time('Y-m-d H:i:s', $id),
            'year' => (string) get_the_time('Y', $id),
            'month' => (string) get_the_time('m', $id),
            'authors' => $authors
        );
    }
}
