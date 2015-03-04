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

      $this->post_ID = get_the_ID();
      $is_imported = get_post_meta($this->post_ID, 'is_imported', true);
      if($is_imported) {
        $this->view('shared/imported_banner');
      }
      $this->view('shared/breadcrumbs');
      $this->view('pages/guidance_and_support_content/main', $this->get_data());

      get_footer();
    }
  }

  function get_data(){
    $this->max_links = 7;
    $this->has_links = false;
    $article_date = get_the_modified_date();
    $post = get_post($this->post_ID);
    if (has_ancestor('about') || $post->post_name=='about' ) {
      $this->page_category = 'About us';
    } else {
      $this->page_category = 'Guidance';
    }

    ob_start();
    the_content();
    $content = ob_get_clean();

    return array(
      'id' => $this->post_ID,
      // 'author' => get_the_author(),
      'author' => "Intranet content team",
      // 'author_email' => get_the_author_meta('user_email'),
      'author_email' => "newintranet@digital.justice.gov.uk",
      'title' => get_the_title(),
      'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
      'content' => $content,
      'raw_date' => $article_date,
      'human_date' => date("j F Y", strtotime($article_date)),
      'redirect_url' => get_post_meta($this->post_ID, 'redirect_url', true),
      'redirect_enabled' => get_post_meta($this->post_ID, 'redirect_enabled', true),
      'link_array' => $this->get_link_array(),
      'tab_array' => $this->get_tab_array(),
      'tab_count' => $this->tab_count,
      'has_q_links' => $this->has_q_links,
      'has_firsttab_links' => $this->has_firsttab_links,
      'has_secondtab_links' => $this->has_secondtab_links,
      'page_category' => $this->page_category
    );
  }

  private function get_link_array() {
    // Populate link array
    $ns = 'quick_links'; // Quick namespace variable
    $link_array = new stdClass();
    $link_array->q_link_array = array();
    $link_array->firsttab_link_array = array();
    $link_array->secondtab_link_array = array();
    $link_meta_exists = true;
    $i=1;

    while ($link_meta_exists) {
      $link_fields = array('link-text','url','qlink','firsttab','secondtab');
      if(metadata_exists( 'post', $this->post_ID, "_" . $ns . "-link-text" . $i )) {
        foreach($link_fields as $link_field) {
            $link_field_transformed = str_replace('-','_',$link_field);
            $$link_field_transformed = get_post_meta($this->post_ID, "_" . $ns . "-" . $link_field . $i,true);
        }
        if ($qlink=='on') {
          $link_array->q_link_array[] = array(
            'linktext' => esc_attr($link_text),
            'linkurl' => esc_attr($url)
          );
          $this->has_q_links = true;
        }
        if ($firsttab=='on') {
          $link_array->firsttab_link_array[] = array(
            'linktext' => esc_attr($link_text),
            'linkurl' => esc_attr($url)
          );
          $this->has_firsttab_links = true;
        }
        if ($secondtab=='on') {
          $link_array->secondtab_link_array[] = array(
            'linktext' => esc_attr($link_text),
            'linkurl' => esc_attr($url)
          );
          $this->has_secondtab_links = true;
        }
        $i++;
      } else {
        $link_meta_exists = false;
      }
    }

    return $link_array;
  }

  private function get_tab_array() {
    // Populate tab array
    $ns = 'content_tabs'; // Quick namespace variable
    $this->tab_count = get_post_meta($this->post_ID,'_'.$ns.'-tab-count',true);

    $tab_array = array();
    for($i=1;$i<=$this->tab_count;$i++) {
      $section_count = get_post_meta($this->post_ID,'_'.$ns.'-tab-' . $i . '-section-count',true);
      $section_array = array();
      for($j=1;$j<=$section_count;$j++) {
        $section_title = get_post_meta($this->post_ID,'_' . $ns . '-tab-' . $i . '-section-' . $j . '-title',true);
        $section_content = get_post_meta($this->post_ID,'_' . $ns . '-tab-' . $i . '-section-' . $j . '-content-html',true);
        $section_array[$j] = array(
          'title' => $section_title,
          'content' => $section_content
        );
      }
      $tab_title = get_post_meta($this->post_ID,'_'.$ns.'-tab-' . $i . '-title', true);
      $tab_title = esc_attr($tab_title);
      $tab_array[$i] = array(
        'title' => $tab_title,
        'name' => str_replace(' ','_',preg_replace('/[^\da-z ]/i', '',strtolower($tab_title))),
        'sections' => $section_array
      );
    }

    return $tab_array;
  }
}

new Page_guidance_and_support();
