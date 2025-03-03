<?php

if (empty($args['post_type']) || empty($args['template'])) {
  return;
}

$archives_args = [
  'type'            => 'monthly',
  'format'          => 'custom',
  'show_post_count' => false,
];

?>

<!-- c-content-filter starts here -->
<?php $prefix = 'ff'; ?>
<section class="c-content-filter">
  <form action="load_search_results" id="<?= $prefix ?>" method="post">
    <fieldset>
      <legend>
        <h2>Filter by</h2>
      </legend>
      <div class="c-input-container c-input-container--select">
        <label for="ff_date_filter">Date</label>
        <select name="ff_date_filter" id="ff_date_filter">
          <option value=""><?= esc_attr(__('All')) ?></option>
          <?php wp_get_archives($archives_args); ?>
        </select>
      </div>
      <?php form_builder('text', $prefix, 'Keywords', 'keywords_filter', null, null, 'Contains words such as &lsquo;food&rsquo;', null, false, null, null); ?>

      <?php 
      // I'm not sure why the field names have a prefix, but let's keep with that and send the prefix so it can be stripped by js. 
      ?>
      <input type="hidden" name="prefix" value="<?= $prefix ?>_" />

      <input type="hidden" name="page" value="1" />

      <?php // Hidden field to pass nonce for improved security. ?>
      <input type="hidden" name="_nonce" value="<?= wp_create_nonce('search_filter_nonce') ?>" />

      <input type="hidden" name="post_type" value="<?= $args['post_type']; ?>" />

      <input type="submit" value="Filter" id="ff_button_submit" />
    </fieldset>
  </form>
</section>
<!-- c-content-filter ends here -->
