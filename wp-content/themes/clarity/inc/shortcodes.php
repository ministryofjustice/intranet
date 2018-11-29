<?php
function dw_columns_shortcode($atts, $content = "")
{
    return '<div class="row">' . apply_filters('the_content', $content) . '</div>';
}
add_shortcode('columns', 'dw_columns_shortcode');

function dw_col_shortcode($atts, $content = "")
{
    return '<div class="l-half-section">' . apply_filters('the_content', $content) . '</div>';
}
add_shortcode('col', 'dw_col_shortcode');


function dw_fix_shortcode_gaps($content)
{
    $block = join("|", array("dw_columns", "dw_col"));
    $rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content);
    $rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/", "[/$2]", $rep);
    return $rep;
}
add_filter("the_content", "dw_fix_shortcode_gaps");

function hr_most_popular_shortcode()
{

  ob_start();
  get_template_part( 'src/components/c-most-popular/view' );
  return ob_get_clean();

}
add_shortcode( 'mostpopular', 'hr_most_popular_shortcode' );
