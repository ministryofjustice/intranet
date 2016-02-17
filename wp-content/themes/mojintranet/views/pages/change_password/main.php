<?php if (!defined('ABSPATH')) die(); ?>

<div class="template-container">
  <div class="grid">
    <div class="col-lg-8 col-md-8 col-sm-12">
      <h1><?=$tpl['page_title_text']?></h1>
      <form class="userform standard reset-password-form">
        <input type="hidden" id="rp_login" name="rp_login" value="<?=$login?>" autocomplete="off" />
        <input type="hidden" id="rp_key" name="rp_key" value="<?=$key?>" />

        <label class="form-row">
          <span class="label"><?=$tpl['enter_password_text']?></span>
          <span class="small-label">Must contain letters, numbers, symbols and be at least 8 characters long.</span>
          <input type="password" name="password" size="20" value="" autocomplete="off" />
          <div class="password-strength">
            <ul class="strength-bar">
              <?php for($a = 1; $a <= 5; $a++): ?>
                <li class="segment segment-<?=$a?>"></li>
              <?php endfor ?>
            </ul>
            <p>
              Password strength: <span class="strength-label"></span>
            </p>
          </div>
        </label>

        <label class="form-row">
          <span class="label"><?=$tpl['reenter_password_text']?></span>
          <input type="password" name="reenter_password" size="20" value="" autocomplete="off" />
        </label>

        <label class="form-row">
          <input type="submit" class="cta cta-positive" value="<?=$tpl['cta_text']?>" />
        </label>

        <?php $this->view('modules/validation/validation') ?>
      </form>
    </div>
  </div>
</div>
