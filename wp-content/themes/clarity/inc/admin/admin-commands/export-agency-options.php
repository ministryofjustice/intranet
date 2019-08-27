<?php

namespace MOJ_Intranet\Admin_Commands;
use MOJ\Intranet\Agency;

class Export_Agency_Options extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Export Agency Options';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Export Agency Home Page Settings and Site Details to JSON';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {

        $oAgency          = new Agency();
        $activeAgencies   = $oAgency->getList();

        $uploads = wp_upload_dir();
        $agencies = ['hq'];
        //$agencies = ['hq', 'hmcts', 'laa', 'opg'];

        if ( file_exists( $uploads["basedir"] . '/content-export') == false ) {
            wp_mkdir_p( $uploads["basedir"] . '/content-export' );
        }

        $fields = acf_get_fields('group_5c10285516106');

        $header_fields = acf_get_fields('group_5c0abb97bd431');



        foreach ( $agencies as $current_agency ) {

            if (file_exists($uploads["basedir"] . '/content-export/' . $current_agency) == false) {
                wp_mkdir_p($uploads["basedir"] . '/content-export/' . $current_agency);
            }



            if (key_exists($current_agency, $activeAgencies)) {

                $site_data = [
                    "slug" => $current_agency,
                    "name" => $activeAgencies[$current_agency]['label'],
                    'abbreviation' => $activeAgencies[$current_agency]['abbreviation']
                ];

                $homepage_options = [];

                foreach ( $fields as $field ) {

                    if(strlen($field["name"]) > 0) {
                        $homepage_options[$field["name"]] = get_field($current_agency . '_' . $field["name"], 'option');
                    }
                }

                $site_data["homepage"] = $homepage_options;

                $header_options = [];

                foreach ( $header_fields as $field ) {

                    if(strlen($field["name"]) > 0) {

                        $field_value = get_field($current_agency . '_' . $field["name"], 'option');
                        if(is_array($field_value)){
                            unset($field_value[""]);
                        }
                        $header_options[$field["name"]] = $field_value;
                    }
                }


                $site_data["header"] = $header_options;


                $fp = fopen($uploads["basedir"] . '/content-export/' . $current_agency . '/site.json', 'w');
                fwrite($fp, json_encode($site_data));
                fclose($fp);
            }
        }

    }
}
