<?php

namespace MOJ\Intranet;

// Exit if this file is included within a WordPress request.
if (defined('ABSPATH')) {
    error_log('moj-auth/401.php was accessed within the context of WordPress.');
    http_response_code(401) && exit();
}

// Do not allow access 401.php
defined('DOING_STANDALONE_401') || exit;

?>
<!DOCTYPE HTML>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="refresh" content="0; url=<?= $this::OAUTH_LOGIN_URI ?>">
        <script type="text/javascript">
            window.location.href = "<?= $this::OAUTH_LOGIN_URI ?>"
        </script>
        <title>Page Redirection</title>
    </head>

    <body>
        <!-- Note: don't tell people to `click` the link, just tell them that it is a link. -->
        If you are not redirected automatically, follow this <a href='<?= $this::OAUTH_LOGIN_URI ?>'>link to login</a>.
    </body>

</html>
