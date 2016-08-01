<?php if (!defined('ABSPATH')) die(); ?>

<div id="feedback-section" class="grid page-feedback-container" data-email="newintranet@digital.justice.gov.uk" data-alt-email="intranet@digital.justice.gov.uk">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <a class="report-problem-link" href="#">Is there anything wrong with this page?</a>
    <div class="form-container">
      <p class="message"></p>
      <form class="feedback-form visible standard">
        <label class="form-row">
          <span class="label">Your name: <span class="required">*</span></span>
          <input type="text" name="name" class="name-field text" />
        </label>
        <label class="form-row">
          <span class="label">Your email: <span class="required">*</span></span>
          <input type="text" name="email" class="email-field text" />
        </label>
        <label class="form-row">
          <span class="label">Describe what's wrong with this page: <span class="required">*</span></span>
          <textarea name="feedback" class="feedback-field"></textarea>
        </label>
        <input class="cta cta-positive report-cta" type="submit" value="Report" />
      </form>
    </div>
  </div>
</div>
