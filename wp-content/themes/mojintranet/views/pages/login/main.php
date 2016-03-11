<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container">
  <div class="grid">
    <div class="col-lg-8 col-md-8 col-sm-12">
      <h1>Sign in</h1>
      <form class="userform standard login-form">
        <label class="form-row">
          <span class="label">Email</span>
          <input type="text" name="email" />
        </label>
        <label class="form-row">
          <span class="label">Password</span>
          <input type="password" name="password" />
        </label>
        <label class="form-row">
          <input type="submit" class="cta cta-positive" value="Sign in">
        </label>

        <?php $this->view('modules/validation/validation') ?>
      </form>

      <ul class="secondary-actions">
        <li>
          <a href="<?=$register_url?>">Register</a>
        </li>
        <li>
          <a href="<?=$forgot_password_url?>">Forgot password?</a>
        </li>
      </ul>
    </div>
  </div>
</div>
