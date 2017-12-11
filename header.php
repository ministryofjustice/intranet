<?php 

    if(is_front_page()){
        header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        header('Pragma: no-cache');
    }

    get_component('c-global-header'); 
?>
