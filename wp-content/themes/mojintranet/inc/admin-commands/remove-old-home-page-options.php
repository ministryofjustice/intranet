<?php

namespace MOJ_Intranet\Admin_Commands;

class Remove_Old_Home_Page_Options extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Remove Old Home Page Options';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Remove the old  homepage options that are no longer used';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {

        //Set counts
        $total_stories = 2;
        $total_slides = 3;

        //Clear Featured Stories
        for($x=1;$x<=$total_stories;$x++) {

            delete_option( 'featured_story'.$x );

        }

        //Clear Need to Know
        for($x=1;$x<=$total_slides;$x++) {

            delete_option( 'need_to_know_headline'.$x );

            delete_option( 'need_to_know_url'.$x);

            delete_option( 'need_to_know_image'.$x );

            delete_option( 'need_to_know_alt'.$x );

        }

        echo '<p>Old Home Page Options Cleared</p>';
    }
}
