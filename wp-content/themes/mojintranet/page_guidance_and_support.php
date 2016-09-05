<?php if (!defined('ABSPATH')) die();


/**
 * The Template for guidance and support pages
 *
 * Template name: Guidance & Support
 */
class Page_guidance_and_support extends MVC_controller {
  function main(){
    $this->model('my_moj');

    while(have_posts()){
      the_post();

      $this->post_ID = get_the_ID();
      $this->view('layouts/default', $this->get_data());
    }
  }

  function get_data(){
    $this->max_links = 7;
    $this->tab_count = 0;
    $article_date = get_the_modified_date();
    $post = get_post($this->post_ID);
    if (has_ancestor('about') || $post->post_name=='about' ) {
      $this->page_category = 'About us';
    } else {
      $this->page_category = 'Guidance';
    }

    $lhs_menu_on = get_post_meta($post->ID, 'lhs_menu_on', true) != "0" ? true : false;

    //$authors = dw_get_author_info($this->post_ID);
    $agencies = get_the_terms($this->post_ID, 'agency');
    $list_of_agencies = [];

    foreach ($agencies as $agency) {
      $list_of_agencies[] = $agency->name;
    }

    ob_start();
    the_content();
    $content = ob_get_clean();

    return array(
      'page' => 'pages/guidance_and_support_content/main',
      'template_class' => 'guidance-and-support-content',
      'cache_timeout' => 60 * 60, /* 60 minutes */
      'page_data' => array(
        'id' => $this->post_ID,
        'agencies' => implode(', ', $list_of_agencies),
        //'author' => $authors[0]['name'],
        'author_email' => "newintranet@digital.justice.gov.uk",
        'last_updated' => date("j F Y", strtotime(get_the_modified_date())),
        'title' => get_the_title(),
        'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
        'content' => $content,
        'raw_date' => $article_date,
        'human_date' => date("j F Y", strtotime($article_date)),
        'redirect_url' => get_post_meta($this->post_ID, 'redirect_url', true),
        'tab_array' => $this->get_tab_array(),
        'tab_count' => $this->tab_count,
        'page_category' => $this->page_category,
        'lhs_menu_on' => $lhs_menu_on,
        'hide_page_details' => (boolean) get_post_meta($this->post_ID, 'dw_hide_page_details', true)
      )
    );
  }

  private function get_tab_array() {
    $guidance_tabs = get_field('guidance_tabs',$this->post_ID);

    if (is_array($guidance_tabs) && count($guidance_tabs) > 0) {
      $this->tab_count = count($guidance_tabs);
      $i = 0;
      foreach ($guidance_tabs as $tab) {
        $guidance_tabs[$i]['name'] = str_replace(' ','_',preg_replace('/[^\da-z ]/i', '',strtolower($tab['tab_title'])));

        $s = 0;
        foreach ($tab['sections'] as $section) { //apply content filters
          if (array_key_exists('section_html_content', $section)) {
            $guidance_tabs[$i]['sections'][$s]['section_html_content'] = apply_filters('the_content', $section['section_html_content']);
          }

          $s++;
        }

        $guidance_tabs[$i]['default_heading'] = true; //if heading is not first link show default heading
        if (is_array($tab['links']) && count($tab['links']) > 0) {
            if ($tab['links'][0]['link_type'] == 'heading') {
              $guidance_tabs[$i]['default_heading'] = false;
            }
        }

        $i++;
      }

    }

    return $guidance_tabs;
  }
}
