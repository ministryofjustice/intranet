<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

?>
<!-- c-events-item-byline starts here -->
<article class="c-events-item-byline">
  <header>

	  <h1><a href="<?php echo $post_url; ?>"><?php echo $event_title; ?></a></h1>


	<?php
	if ( empty( $all_day ) ) {
		if ( isset( $start_time ) || isset( $end_time ) ) {
			$time = $start_time . ' - ' . $end_time;
		} else {
			$time = '';
		}
	} else {
		$time = 'All day';
	}
	?>

	<div class="c-events-item-byline__time">
	  <h2>Time:</h2>
		<?php echo $time; ?>
	</div>

	<?php if ( isset( $location ) ) : ?>

	  <div class="c-events-item-byline__location">
		<h2>Location:</h2>
		<address><?php echo $location; ?></address>
	  </div>

	<?php endif; ?>
  </header>
</article>
<!-- c-events-item-byline ends here -->
