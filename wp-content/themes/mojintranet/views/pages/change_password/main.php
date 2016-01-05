<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container">
  <div class="grid">
    <div class="col-lg-8 col-md-8 col-sm-12">
      <h2><?=$page_title?></h2>
      <?php if($message) { ?>
      <p class="register-message <?=$message_type?>"><?=$message?></p>
      <?php } ?>
      <?php if(!$hide_form) { ?>
      <form class="userform" name="change-password-form" id="change-password-form" action="<?=site_url('wp-login.php?action=resetpass')?>" method="post">
        <input type="hidden" id="rp_login" name="rp_login" value="<?=$login?>" autocomplete="off">
        <input type="hidden" id="rp_key" name="rp_key" value="<?=$key?>">
        <p>
            <label for="pass1">New password</label>
            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off">
        </p>
        <p>
            <label for="pass2">Repeat new password</label>
            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off">
        </p>
        <p class="description"><?php echo wp_get_password_hint(); ?></p>
        <p class="resetpass-submit">
            <input type="submit" name="submit" id="resetpass-button" class="button" value="<?=$page_title?>">
        </p>
      </form>
      <?php } ?>
      <div class="secondary-actions">
        <a href="<?=$login_url?>">Login</a>
      </div>
    </div>
  </div>
</div>