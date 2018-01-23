<?php 
  $archives_args = [
    'type' => 'monthly',
    'format' => 'custom',
    'show_post_count' => false
  ];
?>

<!-- c-content-filter starts here -->
<?php $prefix = 'ff'; ?>
<section class="c-content-filter">
  <p>The results will update automatically based on your selections.</p>
  <form action="" id="<?php echo $prefix; ?>" action="post" data-page="0">
    <div class="c-input-container c-input-container--select">
    <label for="ff_date_filter">Date:</label>
        <select name="ff_date_filter" id="ff_date_filter" >
          <option value=""><?php echo esc_attr( __( 'All' ) ); ?></option>
          <?php
            wp_get_archives( $archives_args );
          ?>
        </select>
    </div>
    <?php
    form_builder('text', $prefix, 'Keywords', 'keywords_filter', null, null, 'Keywords', null, false, null, null);
    ?>
    <input type="submit" value="Filter" id="ff_button_submit" />
  </form>
</section>
<!-- c-content-filter ends here -->
