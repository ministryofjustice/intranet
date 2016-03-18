<?php if (!defined('ABSPATH')) die(); ?>

<div class="form-screen">
  <h1>Forgotten Password</h1>
  <form class="userform standard forgot-password-form">
    <p class="description">Enter your email below and we'll email you instructions on how to reset your password.</p>
    <label class="form-row">
      <span class="label">Email</span>
      <input type="text" name="email" />
    </label>

    <label class="form-row">
      <input type="submit" class="cta cta-positive" value="Continue" />
    </label>

    <?php $this->view('modules/validation/validation') ?>
  </form>
</div>
