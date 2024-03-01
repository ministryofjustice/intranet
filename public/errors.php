<?php

if (getenv('WP_ENV') !== 'development') {
    die('Cannot access this page');
}

error_log('Source function: `error_log`. Source file: errors.php');

trigger_error('Source function: `trigger_error. Source file: errors.php`', E_USER_WARNING);

throw new Exception("Source function: `throw new Exception`. Source file: errors.php", 900);
