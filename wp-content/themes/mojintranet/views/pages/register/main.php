<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container">
  <div class="grid">
    <div class="col-lg-8 col-md-8 col-sm-12">
      <h2>Register</h2>
      <?php if($message) { ?>
      <p class="register-message <?=$message_type?>"><?=$message?></p>
      <?php } ?>
      <form name="registerform" id="registerform" action="<?=site_url('wp-login.php?action=register','login_post')?>" method="post">
        <p>
          <label for="user_email">E-mail</label>
          <input type="text" name="user_email" id="user_email" value="" class="user_email">
          <input type="hidden" name="user_login" value="" class="user_login">
          <input type="hidden" name="redirect_to" value="/register/?status=success">
        </p>

        <p class="register-submit">
          <input type="submit" name="wp-submit" id="wp-submit" value="Register">
        </p>
      </form>
    </div>
  </div>
</div>