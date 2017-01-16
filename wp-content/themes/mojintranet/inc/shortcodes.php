<?php
function dw_columns_shortcode($atts, $content = "" ) {
  return '<div class="grid">' . apply_filters('the_content', $content) . '</div>';
}
add_shortcode('dw_columns', 'dw_columns_shortcode');

function dw_col_shortcode($atts, $content = "" ) {
  return '<div class="col-lg-6 col-md-6 col-sm-12">' . apply_filters('the_content', WPCom_Markdown::get_instance()->transform($content, ['unslash' => false])) . '</div>';
}
add_shortcode('dw_col', 'dw_col_shortcode');


function dw_fix_shortcode_gaps($content) {
  $block = join("|",array("dw_columns", "dw_col"));
  $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
  $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);
  return $rep;
}
add_filter("the_content", "dw_fix_shortcode_gaps");
