<?php $prefix = 'fbf' ?>
<form class="c-feedback-form js-reveal-target" id="<?php echo $prefix ?>" action="<?php the_permalink(); ?>" method="POST">
  <?php
  
  form_builder('text', $prefix, 'Your name', 'name', null, null, 'Enter your name', null, true, null, null);
  form_builder('text', $prefix, 'Your email', 'email', null, null, 'Enter your email', null, true, null, null);
  form_builder('textarea', $prefix, 'Describe what\'s wrong with this page', 'message', null, null, 'Enter your feedback', null, true, null, null);

  // $select_options = array(
  //   array('select 1', 'hello', true),
  //   array('select 2', 'badger', false),
  //   array('select 3', 1, false)
  // );

  //form_builder('select', $prefix, 'Your options', 'your_select', null, null, 'Enter your options', null, true, null, $select_options) ;
  ?>
  <input type="submit" class="o-button" name="submit" type="submit" value="Report">
</form>
