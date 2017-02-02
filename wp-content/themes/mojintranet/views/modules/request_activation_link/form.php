<?php if (!defined('ABSPATH')) die(); ?>

<div class="grid register-form-box not-logged-in-only">
  <div class="col-lg-8 col-md-12 col-sm-12">
    <h3>Comment on this page</h3>

    <form class="userform standard register-form">
      <p class="description">Fill in your details below. We’ll then send you a link back to this page so you can start commenting.</p>

      <div class="form-row">
        <label>
          <span class="label">Screen name (will appear on screen)</span>
          <input type="text" name="display_name" />
        </label>
      </div>

      <div class="form-row">
        <label>
          <span class="label">Email address (will not be shown with your comment)</span>
          <input type="text" name="email" />
        </label>
      </div>

      <div class="form-row">
        <label>
          <input type="submit" class="cta cta-positive" value="Get link">
        </label>
      </div>

      <p class="secondary-action">
        <a href="<?=$commenting_policy_url?>">MoJ commenting policy</a>
      </p>
    </form>
  </div>
</div>

<?php $this->view('modules/validation/validation') ?>
