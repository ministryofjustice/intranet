<?php if (!defined('ABSPATH')) die();

/**
 * The Template for guidance and support pages
 *
 * Template name: Guidance & Support
 */
class Page_guidance_and_support extends MVC_controller {
  function main(){
    while(have_posts()){
      the_post();
      get_header();
      $this->view('shared/breadcrumbs');
      $this->view('pages/guidance_and_support_content/main', $this->get_data());
      get_footer();
    }
  }

  function get_data(){
    $article_date = get_the_modified_date();
    $post = get_post(get_the_id());
    ob_start();
    the_content();
    $content = ob_get_clean();

    $this_id = $post->ID;

    // Populate link array
    $record_count = 0;
    $ns = 'quick_links'; // Quick namespace variable
    for($i=1;$i<=5;$i++) { 
        $link_text = get_post_meta($post->ID, "_" . $ns . "-link-text" . $i,true);
        $link_url = get_post_meta($post->ID, "_" . $ns . "-url" . $i,true);
        if ($link_text!=null || $link_url!=null) {
            $record_count++;
            $link_array[$record_count] = array(
                'linktext' => $link_text,
                'linkurl' => $link_url
            );
        }
    }

    return array(
      'id' => $this_id,
      'author' => get_the_author(),
      'author_email' => get_the_author_meta('user_email'),
      'title' => get_the_title(),
      'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
      'content' => $content,
      'raw_date' => $article_date,
      'human_date' => date("j F Y", strtotime($article_date)),
      'redirect_url' => get_post_meta(get_the_ID(), 'redirect_url', true),
      'redirect_enabled' => get_post_meta(get_the_ID(), 'redirect_enabled', true),
      'link_array' => $link_array
    );
  }
}

new Page_guidance_and_support();
