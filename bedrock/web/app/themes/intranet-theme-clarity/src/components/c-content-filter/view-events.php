<?php

if (!defined('ABSPATH')) {
    die();
}

$prefix = 'ff';
?>

<!-- c-content-filter starts here -->
<section class="c-content-filter">
  <p>The results will update automatically based on your selections.</p>
  <form action="" id="<?php echo $prefix . '_events'; ?>" action="post" data-page="0">
    <div class="c-input-container c-input-container--select">
    <label for="ff_date_filter">Date:</label>
        <select name="ff_date_filter" id="ff_date_filter" >
          <option value="all"><?php echo esc_attr(__('All')); ?></option>
          <?php
            $m = 0;

            while ($m <= 12) {
                $next_month = date('Y-m', strtotime('+'.$m. 'months'));
                $human_date = date('F Y', strtotime('+'.$m. 'months'));
                echo '<option value='.$next_month.'>'.$human_date.'</option>';
                $m++;
            }
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
