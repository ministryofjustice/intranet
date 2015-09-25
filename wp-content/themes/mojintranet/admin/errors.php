<?php

  // Display errors
  function mojintranet_display_errors(){
    $mojintranet_errors = get_option(mojintranet_errors);


    $output='';

    if(!empty($mojintranet_errors)){
      $output='<div class="error">';
      foreach($mojintranet_errors as $msg){
        $output.='<p>'.$msg.'</p>';
      }
      $output.='</div>';
    }

    echo $output;
    delete_option( 'mojintranet_errors' );
  }
  add_action('admin_notices','mojintranet_display_errors');

?>
