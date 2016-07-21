<?php if (!defined('ABSPATH')) die();

$feedback_url = 'https://www.surveymonkey.co.uk/r/8VDHMY8';

?>

<div class="grid">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <div class="beta-banner">
      <a href="<?=Taggr::get_permalink('beta')?>">
        <span class="beta-icon">Beta</span>
      </a>
      <p class="message">
        Tell us what you think and
        <a href="<?=$feedback_url?>" target="_blank" rel="external">help us improve<span class="sr-only"> (link opens in a new browser window)</span></a> the intranet. To report a problem, please use the
        <a href="#feedback-section" class="jump-to-problem-form">link in the footer</a>.
        <br />
        <span class="ie7-warning">
          You're currently using Internet Explorer browser.
          For an improved online experience, please <a href="/about-firefox">use an alternative browser</a>.
        </span>
      </p>
    </div>
  </div>
</div>
