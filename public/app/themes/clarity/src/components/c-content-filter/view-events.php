<?php

if (! defined('ABSPATH')) {
    die();
}

$prefix = 'ff';

// Set an initial date as the 15th of the current month.
// So that we don't get rounding errors when adding months.
$ff_date = new DateTime(date('Y-m-\1\5'));
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

            <?php for ($m = 0 ; $m <= 12; $m++) : ?>
              <option value="<?= $ff_date->format('Y-m') ?>"><?= $ff_date->format('F Y') ?></option>
              <?php $ff_date->modify('+ 1 month'); ?>
            <?php endfor; ?>

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
