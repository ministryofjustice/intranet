<?php if (!defined('ABSPATH')) die(); ?>

<!DOCTYPE html>
<html>
  <body>
    <p>Hi <?=$name?>,</p>
    <p>Your password has been reset successfully. Follow the link below to sign in:</p>
    <a href="<?=$login_url?>">Sign in</a>
  </body>
</html>
