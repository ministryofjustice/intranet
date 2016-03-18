<?php if (!defined('ABSPATH')) die(); ?>

<!DOCTYPE html>
<html>
  <body>
    <p>Hi <?=$name?>,</p>
    <p>It looks like you'd like to reset your password. If you didn't make this request, simply ignore this email.</p>
    <a href="<?=$reset_password_url?>">Reset password</a>
  </body>
</html>
