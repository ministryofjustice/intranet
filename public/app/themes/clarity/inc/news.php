<?php
namespace MOJ\Intranet;

/**
 * Retrieves news related data
 */
class News
{
    public HelperSearch $searchHelper;

    public function __construct()
    {
        $this->searchHelper = new HelperSearch();
    }

    /**
     * Get featured news by agency
     *
     * @param string $agency
     */
    public function getFeaturedNews(string $agency = 'hq')
    {
        // Get the featured IDs for the agency
        $featured_ids = self::getFeaturedIds(get_intranet_code());

        $options = [
            // Paging
            'nopaging' => false,
            'offset' => 0,
            'per_page' => MAX_FEATURED_NEWS,
            // Filters
            'post_type' => ['news', 'post', 'page'],
            'post__in' => $featured_ids,
            'search_orderby' => 'post__in',
        ];

        $data = $this->searchHelper->get_raw($options);
        $data = $this->formatData($data);

        return $data;
    }

    public static function getFeaturedIds($agency = 'hq')
    {
        // Get the featured IDs for the agency
        $featured_ids = [];

        for ($a = 1; $a <= MAX_FEATURED_NEWS; $a++) {
            array_push($featured_ids, get_option($agency . '_featured_story' . $a));
        }
        return $featured_ids;
    }

    /** Get a list of news
     *
     * @param array $options
     * @param bool $exclude_featured
     * @return mixed {Array} Formatted and sanitized results
     */
    public function getNews(array $options = [], bool $exclude_featured = false): mixed
    {
        $cpt = get_post_type();
        $post_id = get_the_ID();
        $region_id = get_the_terms($post_id, 'region');

        if ($region_id) :
            foreach ($region_id as $region) :
                // Current region, ie Scotland, North West etc
                $current_region_id = $region->term_id;
            endforeach;
        endif;

        if ($cpt === 'regional_news') :
            $options['post_type'] = 'regional_news';
            $options['tax_query'] = array(
                array(
                    'taxonomy' => 'region',
                    'field' => 'term_id',
                    'terms' => $current_region_id,
                ),
            );
        else :
            $options['post_type'] = 'news';
        endif;

        $options['search_order'] = 'DESC';
        $options['search_orderby'] = 'date';

        if ($exclude_featured) {
            $options['post__not_in'] = $this->getFeaturedIds(get_intranet_code());
            $options['search_orderby'] = 'post__in';
        }

        $data = $this->searchHelper->get_raw($options);
        $data = $this->formatData($data);

        return $data;
    }

    /** Get a list of team news
     *
     * @param array $options
     * @return mixed {Array} Formatted and sanitized results
     */
    public function getTeamNews(array $options = []): mixed
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
            'search_orderby' => 'post__in',
        ];

        $data = $this->searchHelper->get_raw($options);
        $data = $this->formatData($data);

        return $data;
    }

    private function normalizeOptions($options): array
    {
        $default = [
            'start' => 1,
            'length' => 10,
            'post_type' => 'news',
        ];

        foreach ($options as $key => $value) {
            if ($value) {
                $default[$key] = $value;
            }
        }
        return $default;
    }

    /** Format and trim the raw results
     *
     * @param {Object} $data Raw results object
     * @return mixed {Array} Formatted results
     */
    private function formatData($data): mixed
    {
        $data['results'] = array();

        foreach ($data['raw']->posts as $post) {
            $data['results'][] = $this->formatRow($post);
        }

        unset($data['raw']);

        return $data;
    }

    /** Format a single results row
     *
     * @param {Object} $post Post object
     * @return array {Array} Formatted and trimmed post
     */
    private function formatRow($post): array
    {
        $id = $post->ID;

        $post_object = get_post($id);

        $thumbnail_type = 'thumbnail';
        $thumbnail_id = get_post_thumbnail_id($id);
        $thumbnail = wp_get_attachment_image_src($thumbnail_id, $thumbnail_type);
        $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

        return [
            'id' => $id,
            'title' => get_the_title($id),
            'url' => get_the_permalink($id) ?? 'false',
            'slug' => $post_object->post_name,
            'excerpt' => $post_object->post_excerpt,
            'thumbnail_url' => $thumbnail[0] ?? 'false',
            'thumbnail_alt_text' => $alt_text ?? '',
            'timestamp' => get_the_time('Y-m-d H:i:s', $id) ?? '',
        ];
    }
}
