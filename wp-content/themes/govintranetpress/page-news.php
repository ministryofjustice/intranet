<?php
/* Template name: News landing */

class Page_news extends MVC_controller {
  function main(){
    $data = array('test'=>'abc');

    get_header();
    $this->view('shared/breadcrumbs');
    $this->view('pages/news', $this->get_data());
    get_footer();
  }

  function get_data(){
    return array(
    );
  }
}

new Page_news();
