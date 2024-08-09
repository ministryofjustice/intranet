<?php

add_filter('locale', function(){
    return 'en_GB';
});

add_filter('login_display_language_dropdown', '__return_false');
