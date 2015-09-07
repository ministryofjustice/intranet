<?php if (!defined('ABSPATH')) die();

/**
 * The generic template with LHS navigation
 *
 * Template name: Webchat template
 */

class Single_webchat extends MVC_controller {
  function main(){
    $this->model('my_moj');

    while(have_posts()){
      the_post();

      $this->post_ID = get_the_ID();

      $this->view('layouts/default', $this->get_data());
    }
  }

  function get_data(){
    $post = get_post($this->post_ID);

    ob_start();
    the_content();
    $content = ob_get_clean();

    return array(
      'page' => 'pages/webchat_single/main',
      'template_class' => 'generic-nav',
      'cache_timeout' => 60 * 60, /* 1 hour */
      'page_data' => array(
        'id' => $this->post_ID,
        'title' => get_the_title(),
        'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
        'content' => $content,
        'children_data' => $this->get_children_data(),
        'coveritlive_script' => $this->get_coveritlive_script(get_post_meta($this->post_ID,'_webchat-coveritlive-id',true))
      ),
    );
  }

  private function get_children_data() {
    $id = $this->post_ID;
    $children = array();

    do {
      array_push($children, $this->get_children_from_API($id));
    }
    while($id = wp_get_post_parent_id($id));

    $children = array_reverse($children);

    $top_level = $this->get_children_from_API();
    $top_level['title'] = 'MoJ Intranet';

    array_unshift($children, $top_level);

    return htmlspecialchars(json_encode($children));
  }

  private function get_children_from_API($id = null) {
    $results = new children_request(array($id));
    return $results->results_array;
  }

  private function get_coveritlive_script($coveritlive_id) {
    if($coveritlive_id!==null) {
      $script = "
        <div id='cil-root-stream-$coveritlive_id' class='cil-root'>
          <span class='cil-config-data' title='{
            \"altcastCode\":\"$coveritlive_id\",
            \"server\":\"www.coveritlive.com\",
            \"geometry\":{
              \"width\":\"fit\",
              \"height\":600
            },
            \"configuration\":{
              \"newEntryLocation\":\"top\",
              \"commentLocation\":\"top\",
              \"replayContentOrder\":
              \"chronological\",
              \"pinsGrowSize\":\"on\",
              \"titlePage\":\"on\",
              \"embedType\":\"stream\",
              \"titleImage\":\"/templates/coveritlive/images/buildPage/BusinessImage.jpg\"
            }
          }'>
            &nbsp;
          </span>
        </div>
        <script type='text/javascript'>
          window.cilAsyncInit = function() {
            cilEmbedManager.init()
          };
          (function() {
            if (window.cilVwRand === undefined) {
              window.cilVwRand = Math.floor(Math.random()*10000000);
            }
            var e = document.createElement('script');
            e.async = true;
            var domain = (document.location.protocol == 'http:' || document.location.protocol == 'file:') ? 'http://cdnsl.coveritlive.com' : 'https://cdnslssl.coveritlive.com';
            e.src = domain + '/vw.js?v=' + window.cilVwRand;e.id = 'cilScript-$coveritlive_id';
            document.getElementById('cil-root-stream-$coveritlive_id').appendChild(e);
          }());
        </script>
      ";
      return $script;
    } else {
      return null;
    }
  }
}
