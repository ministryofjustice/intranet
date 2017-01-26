<?php if (!defined('ABSPATH')) die(); ?>

<div class="grid register-form-box not-logged-in-only">
  <div class="col-lg-8 col-md-12 col-sm-12">
    <h3>Request a link to start commenting</h3>
    <form class="userform standard register-form">
      <div class="form-row">
        <label>
          <span class="small-label">Screen name</span>
          <input type="text" name="display_name" />
        </label>
      </div>

      <div class="form-row">
        <label>
          <span class="small-label">Email (will not be displayed with your comment)</span>
          <input type="text" name="email" />
        </label>
      </div>

      <div class="form-row">
        <label>
          <input type="submit" class="cta cta-positive" value="Request link">
        </label>
      </div>

      <p class="secondary-action">
        <a href="<?=$commenting_policy_url?>">MoJ commenting policy</a>
      </p>
    </form>
  </div>
</div>

<?php $this->view('modules/validation/validation') ?>
