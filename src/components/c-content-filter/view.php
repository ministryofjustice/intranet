<!-- c-content-filter starts here -->
<?php $prefix = 'ff' ?>
<section class="c-content-filter">
  <p>The results will update automatically based on your selections.</p>
  <form action="" id="<?php echo $prefix?>">
    <?php
      // An action will need to be added above
      $select_options = array(
        array('May 2016', '05-2016', false)
        array('June 2016', '06-2016', false)
        array('July 2016', '07-2016', false)
        array('August 2016', '08-2016', false)
        array('September 2016', '09-2016', false)
        array('October 2016', '10-2016', false)
        array('November 2016', '11-2016', false)
        array('December 2016', '12-2016', false)
        array('January 2017', '01-2017', false)
        array('February 2017', '02-2017', false)
        array('March 2017', '03-2017', false)
        array('April 2017', '04-2017', false)
      );

      form_builder('select', $prefix, 'Date', 'date_filter', null, null, 'Choose a date', null, true, null, $select_options)
      form_builder('text', $prefix, 'Keywords', 'keywords_filter', null, null, 'Keywords', null, true, null, null);
    ?>
  </form>
</section>
<!-- c-content-filter ends here -->
