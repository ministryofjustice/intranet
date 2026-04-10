<?php

defined('ABSPATH') || die();

/**
 * Content filter for the people-update archive
 *
 * This filter allows filtering by the opg_pillar taxonomy.
 * Compared to other filters, it has a buttons to toggle the filter panel and clear filters. 
 * Custom styles are scoped to the `c-content-filter--people-promise` class.
 */

$pillar_options = [
    ['All', 'any'], // default option
];

$terms = get_terms([
    'taxonomy'   => 'opg_pillar',
    'hide_empty' => false,
]);

if (!is_wp_error($terms)) {
    foreach ($terms as $term) {
        $pillar_options[] = [
            $term->name, // Label
            $term->slug  // Value
        ];
    }
}

?>

<!-- c-content-filter starts here -->
<?php $prefix = 'ff'; ?>
<section class="c-content-filter c-content-filter--people-promise c-content-filter--collapsed js-content-filter">

  <button class="o-text-button c-content-filter__toggle">Filter content</button>

  <form action="load_people_update_filter_results" id="<?= $prefix ?>" method="post">
    <fieldset>
      <legend>
        <h2>Filter by</h2>
      </legend>

      <?php        
       form_builder(
            'radio-group',
            '',
            'Pillar',
            'opg_pillar',
            null,
            'any',
            null,
            null,
            false,
            null,
            $pillar_options
        ); ?>

      <?php // Hidden field to pass nonce for improved security. ?>
      <input type="hidden" name="_nonce" value="<?= wp_create_nonce('search_filter_nonce') ?>" />

      <input type="submit" value="Filter" id="ff_button_submit" />
    </fieldset>

  </form>
  
  <?php // Placeholder for the 'Filter applied: xyz' text. ?>
  <div id="ff_state"></div>

  <button class="o-button c-content-filter__clear" id="ff_clear"">Clear filters</button>
</section>
<!-- c-content-filter ends here -->
