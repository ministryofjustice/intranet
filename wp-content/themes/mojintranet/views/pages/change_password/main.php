<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container">
  <div class="grid">
    <div class="col-lg-8 col-md-8 col-sm-12">
      <h1><?=$page_title?></h1>
      <form class="userform standard reset-password-form">
        <input type="hidden" id="rp_login" name="rp_login" value="<?=$login?>" autocomplete="off">
        <input type="hidden" id="rp_key" name="rp_key" value="<?=$key?>">

        <label class="form-row">
          <span class="label">New password</span>
          <input type="password" name="password" size="20" value="" autocomplete="off">
        </label>

        <label class="form-row">
          <span class="label">Repeat new password</span>
          <input type="password" name="reenter_password" size="20" value="" autocomplete="off">
          <p class="description"><?=wp_get_password_hint()?></p>
        </label>

        <label class="form-row">
          <input type="submit" class="cta cta-positive" value="Create">
        </label>

        <?php $this->view('modules/validation/validation') ?>
      </form>
    </div>
  </div>
</div>
