<?php
function filter_acf_the_content( $value ) { 
    $value = str_replace("<table","<div class='clarity-responsive-table'><table", $value);
    $value = str_replace("<div class='clarity-responsive-table'>\n<div class='clarity-responsive-table'>","<div><div class='clarity-responsive-table'>" ,$value);
    $value = str_replace("</table>","</table></div>", $value);
    return $value; 
}; 
         
add_filter( 'acf_the_content', 'filter_acf_the_content', 10, 1 );

add_filter( 'the_content', 'filter_the_content_in_the_main_loop', 200 );
 
function filter_the_content_in_the_main_loop( $content ) {
    $content = str_replace("<table","<div class='clarity-responsive-table'><table", $content);
    $content = str_replace("<div class='clarity-responsive-table'>\n<div class='clarity-responsive-table'>","<div><div class='clarity-responsive-table'>" ,$content);
    $content = str_replace("</table>","</table></div>", $content);
    return $content;
}