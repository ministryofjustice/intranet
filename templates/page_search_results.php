<?php
/*
* Clarity template Search results
*/
//ToDo: Change name to switcher.php when database changed ?>
<?php get_component('c-global-header'); ?>
  <div id="maincontent" class="u-wrapper l-main l-reverse-order t-search-results">
    <h1 class="o-title o-title--page">Search Results</h1>
    <?php get_component('c-search-bar'); ?>
    <div class="l-secondary">
      <?php get_component('c-search-results-filter'); ?>
      <p>To search news go to the <a href="/news">News</a> page
    </div>
    <div class="l-primary" role="main">
      <h2 class="o-title o-title--section">60 search results</h2>
      <?php get_component('c-search-results'); ?>
      <?php get_component('c-pagination'); ?>
    </div>
  </div>
<?php get_component('c-global-footer'); ?>
