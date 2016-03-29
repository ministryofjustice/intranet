<?php if (!defined('ABSPATH')) die(); ?>

<div class="form-screen">
  <h1>Create an account</h1>
  <form class="userform standard register-form">
    <label class="form-row">
      <span class="label">First name</span>
      <input type="text" name="first_name" />
    </label>
    <label class="form-row">
      <span class="label">Surname</span>
      <input type="text" name="surname" />
    </label>
    <label class="form-row">
      <span class="label">Email address</span>
      <span class="small-label">Enter your MoJ email address. This will be your ID for your account.</span>
      <input type="text" name="email" />
    </label>
    <label class="form-row">
      <span class="small-label">Reenter your email address</span>
      <input type="text" name="reenter_email" />
    </label>
    <label class="form-row">
      <span class="label">Display name</span>
      <span class="small-label">This is how people will see you on the Intranet</span>
      <input type="text" name="display_name" />
    </label>
    <label class="form-row">
      <input type="submit" class="cta cta-positive" value="Create account">
    </label>

    <?php $this->view('modules/validation/validation') ?>
  </form>
</div>
