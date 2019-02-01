<?php
// LEGACY
use MOJ\Intranet\Agency;

$oAgency      = new Agency();
$activeAgency = $oAgency->getCurrentAgency();
// LEGACY END
global $wp;
$current_url = home_url( $wp->request )

?>

<?php $prefix = 'fbf'; ?>
<form class="c-feedback-form js-reveal-target" id="<?php echo $prefix; ?>" action="<?php echo $current_url; ?>" method="POST">
	<?php

	form_builder( 'text', $prefix, 'Your name', 'name', null, null, 'Enter your name', null, true, null, null );
	form_builder( 'text', $prefix, 'Your email', 'email', null, null, 'Enter your email', null, true, null, null );
	form_builder( 'textarea', $prefix, 'Describe what\'s wrong with this page', 'message', null, null, 'Enter your feedback', null, true, null, null );

	?>
  <input type="hidden" value="<?php echo $activeAgency['shortcode']; ?>" name="fbf_agency" id="agency">
  <input type="submit" class="o-button" name="submit" type="submit" value="Report">
</form>
