<?php

namespace MOJ\Intranet;

class CLeftHandMenu
{

    private $post_id;
    private $post_type;
    private $transient_key;

    public function __construct($post_id)
    {
        $this->post_id = $post_id;
        $this->post_type = get_post_type();
        $this->transient_key = 'c-left-hand-menu:list:' . $this->post_type . ':' . $this->post_id;
    }

    /**
     * Get child pages.
     * 
     * @return int[]
     */

    public function getChildPages()
    {
        $child_page_args = [
            'post_parent' => $this->post_id,
            'post_type'   => 'any',
            'numberposts' => 1,
            'post_status' => 'publish',
        ];

        return get_children($child_page_args, ARRAY_N);
    }

    /**
     * Get the list
     * 
     * @return void|string
     */

    public function getList()
    {

        $child_pages = $this->getChildPages();

        if (empty($child_pages)) {
            return;
        }

        // Common arguments for wp_list_pages
        $args = [
            'child_of'    => $this->post_id,
            'post_status' => 'publish',
            'link_after'  => '<span class="dropdown"></span>',
            'order'       => 'ASC',
            'orderby'     => 'menu_order',
            'echo'        => false,
            'title_li'    => 0,
        ];

        if (get_post_type() === 'regional_page') {
            // Custom arguments for regional_page.
            $args['post_type'] = 'regional_page';
        } else {
            // Arguments when not regional_page
            $args = array_merge($args, [
                'depth'       => 0,
                'exclude'     => wp_get_post_parent_id($this->post_id),
            ]);
        }

        return wp_list_pages($args);
    }

    /**
     * Get the list (with cache)
     * 
     * @return void|string
     */

    public function getListWithCache()
    {
        // Is there a list in the transient (cache)?
        $cached_list = get_transient($this->transient_key);

        if ($cached_list) {
            return $cached_list;
        }

        $list = $this->getList();

        if(empty($list)) {
            delete_transient($this->transient_key);
        } else {
            // This could be increased to a very long duration if we delete transients
            // based on admin actions like post creation, update or delete.
            set_transient($this->transient_key, $list, 60 * 5); // 5 minutes
        }

        return $list;
    }
}

$list = (new CLeftHandMenu($post->ID))->getListWithCache($post->Id);

if (!$list) {
    return;
}

?>

<!-- c-left-hand-menu starts here -->
<nav class="c-left-hand-menu js-left-hand-menu">

    <div class="c-left-hand-menu__step_back">
        <?= get_the_title($post->ID) ?>
    </div>
    <ul><?= $list ?></ul>

</nav>
<!-- c-left-hand-menu ends here -->
