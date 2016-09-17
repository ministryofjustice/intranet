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

    $tablist_classes = [
      'small-tabs' => $this->tab_count > 3 ? 'small-tabs' : '',
      'hidden' => $this->tab_count <= 1 ? 'hidden' : ''
    ];

    return array(
      'page' => 'pages/guidance_and_support_content/main',
      'template_class' => 'guidance-and-support-content',
      'cache_timeout' => 60 * 60, /* 60 minutes */
      'page_data' => array(
        'id' => $this->post_ID,
        'tablist_classes' => implode(' ', $tablist_classes),
        'tabs' => $this->get_tab_array(),
        'agencies' => implode(', ', $list_of_agencies),
        //'author' => $authors[0]['name'],
        //'author_email' => "newintranet@digital.justice.gov.uk",
        'last_updated' => date("j F Y", strtotime(get_the_modified_date())),
        'title' => get_the_title(),
        'excerpt' => $post->post_excerpt, // Not using get_the_excerpt() to prevent auto-generated excerpts being displayed
        'lhs_menu_on' => $lhs_menu_on,
        'hide_page_details' => (boolean) get_post_meta($this->post_ID, 'dw_hide_page_details', true)
      )
    );
  }

  private function get_tab_array() {
    $guidance_tabs = get_field('guidance_tabs', $this->post_ID);

    if (!is_array($guidance_tabs)) $guidance_tabs = [];

    $this->tab_count = count($guidance_tabs);

    foreach ($guidance_tabs as $tab_index => $tab) {
      $guidance_tabs[$tab_index]['name'] = str_replace(' ','_',preg_replace('/[^\da-z ]/i', '',strtolower($tab['tab_title'])));
      $guidance_tabs[$tab_index]['hidden_class'] = $tab_index > 0 ? 'hidden' : '';

      foreach ($tab['sections'] as $section_index => $section) { //apply content filters
        if (array_key_exists('section_html_content', $section)) {
          $guidance_tabs[$tab_index]['sections'][$section_index]['section_html_content'] = apply_filters('the_content', $section['section_html_content']);
        }
      }

      //organise links
      $link_groups = [];
      $link_group = [
        'heading' => null,
        'links' => []
      ];

      foreach ($tab['links'] as $link_index => $link) {
        if ($link['link_type'] === 'heading') {
          if (count($link_group['links'])) {
            array_push($link_groups, $link_group);
          }

          $link_group = [
            'heading' => $link['link_title'],
            'links' => []
          ];
        }
        else {
          $link_group['links'][] = [
            'title' => $link['link_title'],
            'url' => $link['link_url']
          ];
        }
      }

      if (count($link_group['links'])) {
        array_push($link_groups, $link_group);
      }

      $guidance_tabs[$tab_index]['link_groups'] = $link_groups;
    }

    return $guidance_tabs;
  }
}
