<?php
// LEGACY
use MOJ\Intranet\Agency;

$oAgency      = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
// LEGACY END
global $wp;
$current_url = home_url($wp->request);
$current_user = wp_get_current_user();
$display_name = $current_user->display_name;
$user_email = $current_user->user_email;
?>

<?php 
  $prefix = 'fbf'
?>

<form class="c-feedback-form js-reveal-target" id="<?= $prefix ?>" action="<?= $current_url; ?>#confirmation-message" method="POST">
    <?php

    form_builder('text', $prefix, 'Your name', 'name', null, $display_name, 'Enter your name', null, true, null, null);
    form_builder('text', $prefix, 'Your email', 'email', null, $user_email, 'Enter your email', null, true, null, null);
    form_builder('textarea', $prefix, 'Describe what\'s wrong with this page', 'message', null, null, 'Enter your feedback', null, true, null, null);

    ?>
  <input type="hidden" value="<?= $activeAgency['shortcode'] ?>" name="fbf_agency" id="agency">
  <input type="submit" class="o-button" name="submit" value="Report">
</form>
