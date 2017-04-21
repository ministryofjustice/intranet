<?php $prefix = 'fbf' ?>
<form class="c-feedback-form js-reveal-target" id="<?php echo $prefix ?>">
  <?php

  form_builder('text', $prefix, 'Your name', 'your_name', null, null, 'Enter your name', null, true, null, null);
  form_builder('text', $prefix, 'Your email', 'your_email', null, null, 'Enter your email', null, true, null, null);
  form_builder('textarea', $prefix, 'Describe what\'s wrong with this page', 'your_feedback', null, null, 'Enter your feedback', null, true, null, null);

  $select_options = array(
    array('select 1', 'hello', true),
    array('select 2', 'badger', false),
    array('select 3', 1, false)
  );

  form_builder('select', $prefix, 'Your options', 'your_select', null, null, 'Enter your options', null, true, null, $select_options) ;
  ?>
  <button class="o-button" type="submit">Report</button>
</form>
