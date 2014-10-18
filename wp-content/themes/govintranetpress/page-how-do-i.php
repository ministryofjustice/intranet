<?php
/* Template name: A-Z */

get_header();

?>

<div class="a-z" data-page-id="<?=get_the_id()?>">
  <div class="categories level-1">
  </div>
  <div class="subcategories level-2">
  </div>
  <div class="links level-3">
  </div>

  <template data-name="a-z-category-item">
    <li>
      <h3 class="title">
        <a></a>
      </h3>
      <p class="description"></p>
    </li>
  </template>
</div>

<?php
get_footer();
