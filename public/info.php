<?php

if (getenv('WP_ENV') !== 'development') {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    exit;
}

## -------------------------------------------------------------------------
## -------------------------------------------------------------------------

# Output the IP address of the client. To make sure ingress is passing it correctly.
if(!empty($_SERVER['REMOTE_ADDR'])) {
    echo 'Your IP address is: ' . $_SERVER['REMOTE_ADDR'];
}


# output all settings concerning the PHP installation
phpinfo();


trigger_error("Triggering a warning for output", E_USER_WARNING);
trigger_error("Triggering a fatal error for output", E_USER_ERROR);
