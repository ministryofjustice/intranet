<?php
namespace MOJ\Intranet;

/**
 * Retrieves news related data
 *
 */

class News
{
    public $searchHelper;

    public function __construct()
    {
        $this->searchHelper = new HelperSearch();
    }

    /**
     * Get featured news by agency
     *
     * @param string $agency
     */
    public function getFeaturedNews($agency = 'hq')
    {
        //Get the featured IDs for the agency
        $featured_ids = News::getFeaturedIds(get_intranet_code());

        $options = [
            // Paging
            'nopaging' => false,
            'offset' => 0,
            'per_page' => MAX_FEATURED_NEWS,
            // Filters
            'post_type' => ['news', 'post', 'page'],
            'post__in' => $featured_ids,
            'search_orderby' => 'post__in'
        ];

        $data = $this->searchHelper->get_raw($options);
        $data = $this->format_data($data);

        return $data;
    }

    public static function getFeaturedIds($agency = 'hq')
    {
        //Get the featured IDs for the agency
        $featured_ids = [];

        for ($a = 1; $a <= MAX_FEATURED_NEWS; $a++) {
            array_push($featured_ids, get_option($agency . '_featured_story' . $a));
        }
        return $featured_ids;
    }
    /** Get a list of news
     * @param {Array} $options Options and filters (see search model for details)
     * @return {Array} Formatted and sanitized results
     */
    public function getNews($options = [], $exclude_featured = false)
    {
        $cpt = get_post_type();
        $post_id = get_the_ID();
        $region_id = get_the_terms($post_id, 'region');

        if ($region_id):
        foreach ($region_id as $region):
            // Current region, ie Scotland, North West etc
            $current_region_id = $region->term_id;
        endforeach;
        endif;

        if ($cpt === 'regional_news'):
        $options['post_type'] = 'regional_news';
        $options['tax_query'] = array(
          array(
            'taxonomy' => 'region',
            'field' => 'term_id',
            'terms' => $current_region_id
          )
        ); else:
        $options['post_type'] = 'news';
        endif;

        $options['search_order'] = 'DESC';
        $options['search_orderby'] = 'date';

        if ($exclude_featured) {
            $options['post__not_in'] = $this->getFeaturedIds(get_intranet_code());
            $options['search_orderby'] = 'post__in';
        }

        $data = $this->searchHelper->get_raw($options);
        $data = $this->format_data($data);

        return $data;
    }

    /** Get a list of team news
     * @param {Array} $options Options and filters (see search model for details)
     * @return {Array} Formatted and sanitized results
     */
    public function getTeamNews($options = [])
    {
        $options['search_order'] = 'DESC';
        $options['search_orderby'] = 'date';

        $options = [
           // Paging
           'nopaging' => false,
           'offset' => 0,
           'per_page' => MAX_FEATURED_NEWS,
           // Filters
           'post_type' => ['team_news'],
           'search_orderby' => 'post__in'
         ];

        $data = $this->searchHelper->get_raw($options);
        $data = $this->format_data($data);

        return $data;
    }

    private function normalize_options($options)
    {
        $default = [
            'start' => 1,
            'length' => 10,
            'post_type' => 'news'
        ];

        foreach ($options as $key=>$value) {
            if ($value) {
                $default[$key] = $value;
            }
        }
        return $default;
    }

    /** Format and trim the raw results
     * @param {Object} $data Raw results object
     * @return {Array} Formatted results
     */
    private function format_data($data)
    {
        $data['results'] = array();

        foreach ($data['raw']->posts as $post) {
            $data['results'][] = $this->format_row($post);
        }

        unset($data['raw']);

        return $data;
    }

    /** Format a single results row
     * @param {Object} $post Post object
     * @return {Array} Formatted and trimmed post
     */
    private function format_row($post)
    {
        $id = $post->ID;

        $post_object = get_post($id);

        $thumbnail_type = 'thumbnail';
        $thumbnail_id = get_post_thumbnail_id($id);
        $thumbnail = wp_get_attachment_image_src($thumbnail_id, $thumbnail_type);
        $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

        return [
            'id' => $id,
            'title' => (string) get_the_title($id),
            'url' => (string) get_the_permalink($id),
            'slug' => (string) $post->post_name,
            'excerpt' => (string) $post->post_excerpt,
            'thumbnail_url' => (string) $thumbnail[0],
            'thumbnail_alt_text' => (string) $alt_text,
            'timestamp' => (string) get_the_time('Y-m-d H:i:s', $id)
        ];
    }
}
