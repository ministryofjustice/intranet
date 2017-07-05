<!-- c-search-results-filter starts here -->
<?php $prefix = 'srf'; ?>
<section class="c-search-results-filter">
  <p>You can use the filters to show only results that match your interests</p>
  <form action="" id="<?php echo $prefix; ?>">
    <?php
      // An action will need to be added above
      $select_options = array(
        array('All', 'all', false),
        array('Pages', 'pages', false),
        array('Forms &amp; documents', 'forms-docs', false)
      );

      form_builder('select', $prefix, 'filter', 'filter_by', null, null, 'Filter by', null, true, null, $select_options);
    ?>
  </form>
</section>
<!-- c-search-results-filter ends here -->
