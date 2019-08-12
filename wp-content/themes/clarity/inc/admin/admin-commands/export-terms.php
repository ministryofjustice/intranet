<?php

namespace MOJ_Intranet\Admin_Commands;

class Export_Terms extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Export Terms';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Export Terms to JSON';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {

        $uploads = wp_upload_dir();

        if ( file_exists( $uploads["basedir"] . '/content-export') == false ) {
            wp_mkdir_p( $uploads["basedir"] . '/content-export' );
        }

        if ( file_exists( $uploads["basedir"] . '/content-export/taxonomies') == false ) {
            wp_mkdir_p( $uploads["basedir"] . '/content-export/taxonomies' );
        }

        $taxonomies = get_taxonomies();
        foreach ( $taxonomies as $taxonomy ) {


            $terms = get_terms( array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ) );

            $i = 0;

            foreach ( $terms as $term ) {

                $agencies = [];

                $agencies_ids = get_field('term_used_by', $taxonomy . '_' . $term->term_id);

                if(is_array($agencies_ids) && count($agencies_ids) > 0) {
                    foreach($agencies_ids as $agency_id) {
                        if($agency_id > 0) {
                            $agency = get_term_by('id', $agency_id, 'agency');
                            $agencies[] = $agency->slug;
                        }
                    }

                    if(count($agencies) > 0) {
                        $terms[$i]->agencies = $agencies;
                    }
                }

                $i++;

            }


            $fp = fopen($uploads["basedir"] . '/content-export/taxonomies/' . $taxonomy . '.json', 'w');
            fwrite($fp, json_encode($terms));
            fclose($fp);
        }

    }
}
