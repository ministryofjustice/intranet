<?php
  use MOJ\Intranet\Agency;

$oAgency        = new Agency();
  $activeAgency = $oAgency->getCurrentAgency();
  $agency       = $activeAgency['shortcode'];

  $archives_args = [
      'type'            => 'monthly',
      'format'          => 'custom',
      'show_post_count' => false,
  ];
    ?>

<!-- c-content-filter starts here -->
<?php $prefix = 'ff'; ?>
<section class="c-content-filter">
  <form action="" id="<?= $prefix ?>" action="post" data-page="0">
    <fieldset>
      <legend><p>Filter by</p></legend>  
      <div class="c-input-container c-input-container--select">
        <label for="ff_date_filter">Date</label>
        <select name="ff_date_filter" id="ff_date_filter" >
          <option value=""><?= esc_attr(__('All')) ?></option>
          <?php
            wp_get_archives($archives_args);
          ?>
        </select>
      </div>
      <?php
        $nonce = wp_create_nonce('search_filter_nonce');

        form_builder('text', $prefix, 'Keywords', 'keywords_filter', null, null, 'Contains words such as &lsquo;food&rsquo;', null, false, null, null);

        // Hidden field to pass nonce for improved security
        form_builder('hidden', '', false, '_nonce', '_search_filter_wpnonce', $nonce, null, null, false, null, null);
      ?>
          <div class="c-input-container">
              <label for="ff_categories_filter">Categories    </label>
              <div class="c-input-box">
                <div class="box c-input-container--checkbox">
                  <label>e-News<input type="radio" name="ff_categories_filter_news-category" id="ff_categories_filter_e-news" value="748" checked></label>
                </div>
              </div>
          </div>
      <input type="submit" value="Filter" id="ff_button_submit" />
    </fieldset>   
  </form>
</section>
<!-- c-content-filter ends here -->
