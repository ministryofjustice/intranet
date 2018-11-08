<?php

namespace MOJ_Intranet\Admin_Commands;

class Assign_Agency_Terms_To_Posts extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Assign Agency Terms to Posts';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Assign the HQ agency term to all posts which don\'t have agency terms assigned.';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {
        global $wpdb;

        $post_types = array(
            'news',
            'post',
            'page',
            'webchat',
            'event',
            'document',
            'snippet',
        );

        foreach ($post_types as $post_type) {
            $posts = $wpdb->get_results('SELECT id, post_title FROM wp_posts WHERE post_type = "' . $post_type . '"');

            echo '<h3>Post type: ' . $post_type . '</h3>';
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
