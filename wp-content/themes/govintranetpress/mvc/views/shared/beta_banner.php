<?php if (!defined('ABSPATH')) die();

$feedback_url = $_SESSION['full_site'] ? '' : 'https://www.surveymonkey.com/r/HQImicro';

?>

<div class="grid">
  <div class="col-lg-12 col-md-12 col-sm-12">
    <div class="beta-banner">
      <span class="beta-icon">Alpha</span>
      <p class="message">
        This is a trial service - your
        <a href="<?=$feedback_url?>" target="_blank" rel="external">feedback</a>
        will help us to improve it.
        This site is not optimised for IE7, please use Firefox.
      </p>
    </div>
  </div>
</div>
