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
    $post_ID = get_the_ID();
    $ns = 'quick_links'; // Quick namespace variable
    $article_date = get_the_modified_date();
    $post = get_post($post_ID);
    ob_start();
    the_content();
    $content = ob_get_clean();

    $this_id = $post->ID;

    // Populate link array
    for($i=1;$i<=5;$i++) {
        $link_text = get_post_meta($post->ID, "_" . $ns . "-link-text" . $i,true);
        $link_url = get_post_meta($post->ID, "_" . $ns . "-url" . $i,true);
        if ($link_text!=null || $link_url!=null) {
            $link_array[$i] = array(
                'linktext' => $link_text,
                'linkurl' => $link_url
            );
        }
    }

    // Populate tab array
    $ns = 'content_tabs'; // Quick namespace variable
    $tab_count = get_post_meta($post_ID,'_'.$ns.'-tab-count',true);
    for($i=1;$i<=$tab_count;$i++) {
      $section_count = get_post_meta($post_ID,'_'.$ns.'-tab-' . $i . '-section-count',true);
      for($j=1;$j<=$section_count;$j++) {
        $section_title = get_post_meta($post_ID,'_' . $ns . '-tab-' . $i . '-section-' . $j . '-title',true);
        $section_content = get_post_meta($post_ID,'_' . $ns . '-tab-' . $i . '-section-' . $j . '-content-html',true);
        $section_array[$j] = array(
            'title' => $section_title,
            'content' => $section_content
          );
      }
      $tab_title = get_post_meta($post_ID,'_'.$ns.'-tab-' . $i . '-title',true);
      $tab_array[$i] = array(
          'title' => $tab_title,
          'sections' => $section_array
        );
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
      'redirect_url' => get_post_meta($post_ID, 'redirect_url', true),
      'redirect_enabled' => get_post_meta($post_ID, 'redirect_enabled', true),
      'link_array' => $link_array,
      'tab_count' => $tab_count,
      'tab_array' => $tab_array
    );
  }
}

new Page_guidance_and_support();
