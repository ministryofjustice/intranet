<?php
  use MOJ\Intranet\Agency;

$oAgency         = new Agency();
  $activeAgency  = $oAgency->getCurrentAgency();
  $agency        = $activeAgency['shortcode'];
  $prefix        = 'ff';
  $archives_args = [
	  'type'            => 'monthly',
	  'format'          => 'custom',
	  'show_post_count' => false,
  ];

  // $terms = get_terms( 'region' );
  // if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
  //
  // foreach ( $terms as $term ) {
  // echo '<label>'. $term->name .'<input type="radio" name="ff_categories_filter_news-category" id="ff_categories_filter_'. $term->name .'" value="'. $term->term_id .'"></label>';
  // get current taxonomy name & id
  $term_list = wp_get_post_terms( $post->ID, 'region' );

  $term_id   = $term_list[0]->term_id;
  $term_name = $term_list[0]->name;

	?>

<!-- c-content-filter starts here -->
<section class="c-content-filter">
  <p>Search archived regional news stories.</p>
  <form action="" id="<?php echo $prefix; ?>" action="post" data-page="0">
	<div class="c-input-container c-input-container--select">
	<label for="ff_region_news_date_filter">Date:</label>
		<select name="ff_region_news_date_filter" id="ff_region_news_date_filter" >
		  <option value=""><?php echo esc_attr( __( 'All' ) ); ?></option>
			<?php
			wp_get_archives( $archives_args );
			?>
		</select>
	</div>
	<?php
	$nonce = wp_create_nonce( 'search_filter_nonce' );

	form_builder( 'text', $prefix, 'Keywords', 'keywords_filter', null, null, 'Keywords', null, false, null, null );
	// Hidden field to pass nonce for improved security
	form_builder( 'hidden', '', false, '_nonce', '_search_filter_wpnonce', $nonce, null, null, false, null, null );

	?>

	<input type="hidden" name="ff_categories_filter_regions" id="ff_categories_filter_<?php echo $term_name; ?>" value="<?php echo $term_id; ?>">

	<input type="submit" value="Filter" id="ff_button_submit" />
  </form>
</section>
<!-- c-content-filter ends here -->
