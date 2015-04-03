<?php if (!defined('ABSPATH')) die();
/* Template name: Guidance & Support Index */

class Page_guidance_and_support extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      get_header();
      $this->view('shared/breadcrumbs');
      $this->view('pages/guidance_and_support/main', $this->get_data());
      get_footer();
    }
  }

  function get_data(){
    $levels = array();
    $post_id = get_the_id();
    $ids = get_post_ancestors($post_id);
    $ids = array_reverse($ids);
    array_push($ids, $post_id);

    //get JSON data
    foreach($ids as $key=>$id){
      $levels[$key] = $this->get_children_from_API($ids[$key]);
    }

    //get the slug of the top page - we need this for deep-linking (JS)
    $top_level_post = get_post($ids[0]);

    return array(
      'top_slug' => htmlspecialchars($top_level_post->post_name),
      'levels' => $levels,
      'ids' => $ids
    );
  }

  private function get_children_from_API($id){
    $results = new children_request(array($id));
    return htmlspecialchars(json_encode($results->results_array));
  }
}

new Page_guidance_and_support();
