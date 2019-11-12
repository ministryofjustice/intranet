<?php
namespace MOJ\Intranet;

/**
 * Retrieves and returns guidance and form related data
 */

class GuidanceAndForms
{

    private $page_meta;

    public function __construct()
    {
        $this->page_meta = [
            'post_id' => get_the_ID(),
            'agency'  => get_intranet_code(),
        ];
    }
    /**
     *
     * Returns all child pages placed under the guidance and forms template.
     * Filters posts by the agency you are current viewing the page as. See tax_query parm below.
     *
     * @param
     * @return
     */
    public function get_guidance_and_forms_pages()
    {
        $args = [
            'post_parent' => $this->page_meta['post_id'],
            'orderby'     => 'title',
            'order'       => 'ASC',
            'post_type'   => 'page',
            'numberposts' => -1,
            'post_status' => 'publish',
            'tax_query'   => [
                [
                    'taxonomy' => 'agency',
                    'field'    => 'slug',
                    'terms'    => [
                        $this->page_meta['agency'],
                    ],
                ],
            ],
        ];

        $posts = get_posts($args);

        return $posts;
    }
}
