<!-- c-content-filter starts here -->
<?php $prefix = 'ff'; ?>
<section class="c-content-filter">
  <p>The results will update automatically based on your selections.</p>
  <form action="" id="<?php echo $prefix; ?>" action="post">
    <div class="c-input-container c-input-container--select">
    <label for="ff_date_filter">Date<span class="c-input-container--required">*</span>
    :</label>
        <select name="ff_date_filter" id="ff_date_filter" required="required">
            <?php
              wp_get_archives( $archives_args );
            ?>
        </select>
    </div>
    <?php
    form_builder('text', $prefix, 'Keywords', 'keywords_filter', null, null, 'Keywords', null, true, null, null);
    ?>
  </form>
</section>
<!-- c-content-filter ends here -->
