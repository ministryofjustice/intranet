<?php if (!defined('ABSPATH')) {
    die();
}

class Taggr
{
    private static $property_name = 'dw_tag';

  /** Gets the ID of a post with the given tag name
   * @param {String} $tag_name Tag name
   * @return {Integer} ID of the first post with a matching tag name
   */
    static function get_id($tag_name)
    {
        $post = Taggr::get_post($tag_name);

        return is_object($post) ? $post->ID : null;
    }

  /** Gets the post with the given tag name
   * @param {String} $tag_name Tag name
   * @return {Object} First post with a matching tag name
   */
    static function get_post($tag_name)
    {
        $args = array(
        'meta_key' => Taggr::$property_name,
        'meta_value' => $tag_name,
        'post_type' => 'any'
        );
        $posts = get_posts($args);

        return get_array_value($posts, 0, []);
    }

    static function get_permalink($tag_name)
    {
        $post = Taggr::get_post($tag_name);

        return get_permalink($post);
    }

  /** Gets the current post's tag name
   * @return {String} Tag name
   */
    static function get_current()
    {
        wp_reset_query();

        return get_post_meta(get_the_id(), Taggr::$property_name, true);
    }

  /** Checks whether current post's tag is equal to the supplied tag name
   * @param {String} $tag_name Tag name
   * @return {Boolean}
   */
    static function is_current($tag_name)
    {
        return Taggr::get_current() === $tag_name;
    }

  /** Gets the tag of the supplied post ID
   * @param {Integer} $post_id Post ID
   * @return {String|Null} Posts's tag name or null if it doesn't have a tag
   */
    static function get_tag($post_id)
    {
        $tag = get_post_meta($post_id, 'dw_tag', true);

        if (!strlen($tag)) {
            $tag = null;
        }

        return $tag;
    }
}
