<?php if (!defined('ABSPATH')) die();

class Export_guidance_children extends MVC_controller {
  function main() {
    $data = $this->model->page_tree->get_guidance_children_list();

    $output = fopen("php://output", 'w') or die("Can't open php://output");
    header("Content-Type:application/csv");
    header("Content-Disposition:attachment;filename=export.csv");

    fputcsv($output, array('ID', 'Parent ID', 'Title', 'URL'));

    foreach($data as $row) {
      fputcsv($output, $row);
    }

    fclose($output) or die("Can't close php://output");

    exit;
  }
}
