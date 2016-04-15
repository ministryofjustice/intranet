<?php

namespace MOJ_Intranet\Admin_Commands;

class Assign_Agency_Home_Page_Options extends Admin_Command {
    /**
     * Name of the command.
     *
     * @var string
     */
    public $name = 'Assign Agency Home Page Options';

    /**
     * Description of what this command will do.
     *
     * @var string
     */
    public $description = 'Assign the HQ agency homepage options (for need to know and featured news) using the original global agency options.';

    /**
     * Method to execute the command.
     *
     * @return void
     */
    public function execute() {

        //Set counts
        $total_stories = 2;
        $total_slides = 3;

        //Set Featured Stories
        for($x=1;$x<=$total_stories;$x++) {

            $featured_story = get_option( 'featured_story'.$x );

            if($featured_story) {  update_option( 'hq_featured_story'.$x, $featured_story );  }

        }

        //Set Need to Know
        for($x=1;$x<=$total_slides;$x++) {

            $slide_headline = get_option( 'need_to_know_headline'.$x );

            if($slide_headline) {  update_option( 'hq_need_to_know_headline'.$x, $slide_headline );  }

            $slide_url = get_option( 'need_to_know_url'.$x );

            if($slide_url) {  update_option( 'hq_need_to_know_url'.$x, $slide_url );  }

            $slide_image = get_option( 'need_to_know_image'.$x );

            if($slide_image) {  update_option( 'hq_need_to_know_image'.$x, $slide_image );  }

            $slide_alt = get_option( 'need_to_know_alt'.$x );

            if($slide_alt) {  update_option( 'hq_need_to_know_alt'.$x, $slide_alt );  }

        }

        echo '<p>HQ Home Page Options Set</p>';
    }
}
