<?php

function dw_change_language($locale)
{
    return 'en-GB';

    die();
}
add_filter('locale', 'dw_change_language');
