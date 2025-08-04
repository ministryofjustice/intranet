<?php

if (! defined('ABSPATH')) {
    die();
}

$prefix = 'ff';
?>

<!-- c-content-filter starts here -->
<section class="c-content-filter">
  <form action="load_events_filter_results" id="<?= $prefix . '_events' ?>" method="post">
    <fieldset>
      <legend><h2>Filter by</h2></legend>  
      <div class="c-input-container c-input-container--select">
        <label for="ff_date_filter">Date</label>
          <select name="ff_date_filter" id="ff_date_filter" >
            <option value="all"><?= esc_attr(__('All')) ?></option>
            <?php
              $m = 0;

              // Set a base timestamp as the 15th of the current month.
              // So that we don't get rounding errors when adding months.
              $base_timestamp = strtotime("15 " . date('F Y'));

              $base_timestamp = strtotime("31 July 2024");

              while ($m <= 12) {
                $next_month = date('Y-m', strtotime('+' . $m . 'months', $base_timestamp));
                $human_date = date('F Y', strtotime('+' . $m . 'months', $base_timestamp));
                echo '<option value=' . $next_month . '>' . $human_date . '</option>';
                $m++;
              }
            ?>
        </select>
      </div>
      <?php
        $nonce = wp_create_nonce('search_filter_nonce');

        form_builder('text', $prefix, 'Keywords', 'keywords_filter', null, null, 'Contains words such as &lsquo;induction&rsquo;', null, false, null, null);
        // Hidden field to pass nonce for improved security
        form_builder('hidden', '', false, '_nonce', '_search_filter_wpnonce', $nonce, null, null, false, null, null);
      ?>

      <?php 
      // I'm not sure why the field names have a prefix, but let's keep with that and send the prefix so it can be stripped by js. 
      ?>
      <input type="hidden" name="prefix" value="<?= $prefix ?>_" />

      <input type="submit" value="Filter" id="ff_button_submit" />
    </fieldset>  
  </form>
</section>
<!-- c-content-filter ends here -->
