<?php
function dw_register_main_menu() {
    register_nav_menu('main-menu', 'Main Menu');
}
add_action('init', 'dw_register_main_menu');
