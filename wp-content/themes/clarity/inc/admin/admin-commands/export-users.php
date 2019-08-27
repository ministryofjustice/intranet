<?php

namespace MOJ_Intranet\Admin_Commands;

class Export_Users extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Export Users';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Export Users to JSON';

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

        if ( file_exists( $uploads["basedir"] . '/content-export/users') == false ) {
            wp_mkdir_p( $uploads["basedir"] . '/content-export/users' );
        }

        global $wp_roles;
        $all_roles = $wp_roles->roles;

        foreach ($all_roles as  $role_key => $role_details) {
            $users = get_users( array('role' => $role_key) );

            $count = 0;
            $multiple_count = 0;
            foreach ($users as $user){
                $user_meta = get_user_meta($user->ID);
                $users[$count]->meta = $user_meta;

                $agencies = wp_get_object_terms($user->ID, 'agency');

                $slugs = array_map(function($term) {
                    return $term->slug;
                }, $agencies);

                $users[$count]->agencies = $slugs;

                $count++;
            }


            $fp = fopen($uploads["basedir"] . '/content-export/users/' . $role_key . '.json', 'w');
            fwrite($fp, json_encode($users));
            fclose($fp);

            echo "Exported Users [ Role: " . $role_key . "]<br/>";
        }

    }
}
