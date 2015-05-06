<?php

class Single extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();

      $this->view('layouts/default', $this->get_data());
    }
  }

  private function get_data() {
    return array(
      'page' => 'pages/guidance_and_support/main',
      'page_data' => array(
        'title' => get_the_title()
      )
    );
  }
}