<?php

namespace MOJIntranet\AdminCommands;

class AssignAgencyTermsToPosts extends AdminCommand
{
    public $name = 'Assign Agency Terms to Posts';

    public $description = 'Assign the HQ agency term to all posts which don\'t have agency terms assigned.';

    public function execute()
    {
        global $wpdb;

        $postTypes = array(
            'news',
            'post',
            'page',
            'webchat',
            'event',
            'document',
            'snippet',
        );

        foreach ($postTypes as $postType) {
            $posts = $wpdb->get_results('SELECT id, post_title FROM wp_posts WHERE post_type = "' . $postType . '"');

            echo '<h3>Post type: ' . $postType . '</h3>';
            echo '<ul>';

            foreach ($posts as $post) {
                $terms = wp_get_object_terms($post->id, 'agency');

                if (!empty($terms)) {
                    // This post already has agency terms assigned; skip it.
                    echo '<li style="opacity:0.6"><em>Skipping post: "' . $post->post_title . '" (' . $post->id . ')</em>';
                    continue;
                }

                echo '<li>Applying terms for post: "' . $post->post_title . '" (' . $post->id . ')</li>';
                wp_set_object_terms($post->id, 'hq', 'agency');
            }

            echo '</ul>';
        }
    }
}
