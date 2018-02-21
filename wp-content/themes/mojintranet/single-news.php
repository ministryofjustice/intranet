<?php if (!defined('ABSPATH')) die();

class Single_news extends MVC_controller {
    public function main() {
        while (have_posts()) {
            the_post();
            $this->view('layouts/default', $this->get_data());
        }
    }

    public function get_data()  {
        global $post;
        $article_date = get_the_date();

        ob_start();
        the_content();
        $content = ob_get_clean();
        $this_id = get_the_ID();

        $thumbnail_id = get_post_thumbnail_id($this_id);
        $thumbnail = wp_get_attachment_image_src($thumbnail_id, 'intranet-large');
        $alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
        $authors = dw_get_author_info($this_id);

        $this_id = $post->ID;

        $this->add_global_view_var('commenting_policy_url', site_url('/commenting-policy/'));
        $this->add_global_view_var('comments_open', (boolean) comments_open($this_id));
        $this->add_global_view_var('comments_on', (boolean) get_post_meta($this_id, 'dw_comments_on', true));
        $this->add_global_view_var('logout_url', wp_logout_url($_SERVER['REQUEST_URI']));
      

      return array(
      'page' => 'pages/news_single/main',
      'template_class' => 'single-news',
      'page_data' => array(
        'id' => $this_id,
        'thumbnail' => $thumbnail[0],
        'thumbnail_alt_text' => $alt_text,
        'thumbnail_caption' => get_post_thumbnail_caption(),
        'author' => $authors[0]['name'],
        'author_thumbnail_url' => $authors[0]['thumbnail_url'],
        'job_title' => $authors[0]['job_title'],
        'title' => get_the_title(),
        'excerpt' => get_the_excerpt(),
        'content' => $content,
        'raw_date' => $article_date,
        'human_date' => date("j F Y", strtotime($article_date)),
        'share_bar' => [
          'share_email_body' => "Hi there,\n\nI thought you might be interested in this page I've found on the MoJ intranet:\n",
        ],
        'election_banner' => array(
          'visible' => strtotime($article_date) < strtotime('9 May 2015')?1:0
        )
      )
    );
    }

}
