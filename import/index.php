<?php

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);


require('../wp-blog-header.php');

class Import {
  const REGEX = '/^([- ]+)/';
  private $rows = array();

  function __construct($source_file, $super_parent_id, $base_level) {
    $this->rows[] = array(
      'absolute_level' => $base_level,
      'id' => $super_parent_id
    );

    $this->base_level = $base_level;
    $this->source_file = $source_file;
    $this->last_level_3_offset = null;

    //$this->pre_print(file_get_contents($this->source_file));

    $this->get_from_csv();

    $this->insert_into_db();

    $this->pre_print($this->rows);
  }

  function get_from_csv() {
    if (($handle = fopen($this->source_file, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $title = $data[0];
        $url = $data[1];

        //get level
        preg_match(self::REGEX, $title, $matches);
        $level = substr_count($matches[0], '-');
        //$this->pre_print($level);

        //clean up the title
        $title = preg_replace(self::REGEX, '', $title);

        $absolute_level = $level + $this->base_level + 1;

        $row_data = array(
          'parent_id' => $parent_id,
          'title' => $title,
          'url' => $url,
          'level' => $level,
          'absolute_level' => $absolute_level,
          'keywords' => array()
        );

        //$row_data['id'] = $this->insert($row_data);
        $this->rows[] = $row_data;

        if($absolute_level == 3) {
          $this->last_level_3_offset = count($this->rows) - 1;
        }
        elseif($absolute_level > 3) {
          $this->add_keywords($title);
        }
      }
      fclose($handle);
    }
  }

  private function add_keywords($title) {
    $keywords = $this->rows[$this->last_level_3_offset]['keywords'];
    $title = strtolower($title);
    $title = preg_replace('/[^A-Za-z0-9]+/', ' ', $title);
    $title = preg_replace('/\s+/', ' ', $title);
    $title = trim($title);
    $new_keywords = explode(' ', $title);
    $keywords = array_merge($keywords, $new_keywords);
    $keywords = array_unique($keywords);

    $this->rows[$this->last_level_3_offset]['keywords'] = $keywords;
  }

  private function insert_into_db() {
    foreach($this->rows as $offset=>&$row) {
      if($offset == 0) continue;

      $row['parent_id'] = $this->get_parent_id($offset, $row['absolute_level']);
      $id = $this->insert_row($row);
      $row['id'] = $id;
      //$this->pre_print($row);
    }
  }

  private function insert_row($data) {
    $post_data = array(
      'post_parent' => $data['parent_id'],
      'post_title' => $data['title'],
      'post_status' => 'publish',
      'post_type' => 'page'
    );

    $post_id = wp_insert_post($post_data);

    add_post_meta($post_id, 'redirect_url', $data['url'], true);
    add_post_meta($post_id, 'redirect_enabled', true, true);
    add_post_meta($post_id, '_wp_page_template', 'page-guidance-and-support.php', true);
    add_post_meta($post_id, 'keywords', implode(' ', $data['keywords']), true);

    return $post_id;

    //return rand(1, 1000);
  }

  private function get_parent_id($offset, $absolute_level) {
    //get nearest previous row that is one level higher
    for($a = $offset; $a >= 0; $a--) {
      $row = $this->rows[$a - 1];
      if($row['absolute_level'] == $absolute_level - 1) {
        return $row['id'];
      }
    }

    return null;
  }

  function pre_print($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
  }
}

new Import('migration-hr.csv', 265, 1);
new Import('migration-rest.csv', 131, 0);
