<?php

namespace MOJ_Intranet\Admin_Commands;
use MOJ\Intranet\Agency;

class Export_Agency_Content extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Export Agency Content';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Export Agency Content to JSON';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {

        $oAgency          = new Agency();
        $activeAgencies   = $oAgency->getList();

        $uploads = wp_upload_dir();
        //$post_types = ['news', 'page', 'post', 'event'];
        //$post_types = ['document'];
        //$agencies = ['hq', 'hmcts', 'laa', 'judicial-office'];

        $post_types = ['page'];
        $agencies = ['hq', 'hmcts', 'laa', 'opg'];

        if ( file_exists( $uploads["basedir"] . '/content-export') == false ) {
            wp_mkdir_p( $uploads["basedir"] . '/content-export' );
        }

        foreach ( $agencies as $current_agency ) {

            if (file_exists($uploads["basedir"] . '/content-export/' . $current_agency) == false) {
                wp_mkdir_p($uploads["basedir"] . '/content-export/' . $current_agency);
            }

            foreach ($post_types as $post_type) {

                $posts_list = [];
                $posts_revisions = [];
                $pt_taxonomies = get_object_taxonomies($post_type);

                $posts = get_posts(
                    array(
                        'post_type' => $post_type,
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'agency',
                                'field' => 'slug',
                                'terms' => $current_agency,
                            ),
                        ),
                    )
                );



                foreach ($posts as $post) {
                    setup_postdata($post);

                    $meta = get_post_meta($post->ID);

                    $post->meta = $meta;

                    $post->taxonomies = [];

                    foreach ($pt_taxonomies as $pt_taxonomy) {
                        $terms = wp_get_post_terms($post->ID, $pt_taxonomy);
                        $post->taxonomies[$pt_taxonomy] = $terms;
                    }

                    $posts_list[] = $post;


                    $revisions = wp_get_post_revisions($post->ID);

                    if(count($revisions) > 0){

                        if (file_exists($uploads["basedir"] . '/content-export/' . $current_agency . '/revisions') == false) {
                            wp_mkdir_p($uploads["basedir"] . '/content-export/' . $current_agency . '/revisions');
                        }

                        $fp = fopen($uploads["basedir"] . '/content-export/' . $current_agency . '/revisions/' . $post->ID . '.json', 'w');
                        fwrite($fp, json_encode($revisions));
                        fclose($fp);
                    }


                }
                wp_reset_postdata();

                $fp = fopen($uploads["basedir"] . '/content-export/' . $current_agency . '/' . $post_type . '.json', 'w');
                fwrite($fp, json_encode($posts_list));
                fclose($fp);

                if(count($posts_revisions) > 0){
                    if (file_exists($uploads["basedir"] . '/content-export/' . $current_agency . '/revisions') == false) {
                        wp_mkdir_p($uploads["basedir"] . '/content-export/' . $current_agency . '/revisions');
                    }

                    foreach ($posts_revisions as $post_id => $revisions) {
                        $fp = fopen($uploads["basedir"] . '/content-export/' . $current_agency . '/revisions/' . $post_id . '.json', 'w');
                        fwrite($fp, json_encode($revisions));
                        fclose($fp);
                    }
                }

                echo "Exported CPT [" . $post_type . "] for Agency [" . $current_agency . "]<br/>";
            }
        }


    }
}
