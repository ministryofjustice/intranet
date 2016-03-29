<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container">
  <div class="grid">
    <div class="col-lg-6 col-md-8 col-sm-12">
      <h1>Sign in</h1>
      <form class="userform standard login-form">
        <div class="form-row">
          <label>
            <span class="label">Email</span>
            <input type="text" name="email" />
          </label>
        </div>

        <div class="form-row">
          <label>
            <span class="label">Password</span>
            <input type="password" name="password" />
          </label>
          <p class="field-action">
            <a href="<?=$forgot_password_url?>">Forgotten your password?</a>
          </p>
        </div>

        <div class="form-row">
          <label>
            <input type="submit" class="cta cta-positive" value="Sign in">
          </label>
        </div>

        <?php $this->view('modules/validation/validation') ?>

        <p class="secondary-action">
          <a class="register-link" href="<?=$register_url?>">Register for an account</a>
        </p>
      </form>
    </div>
  </div>
</div>
