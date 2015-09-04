<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container"
     data-page-id="<?=$id?>"
     data-children-data="<?=$children_data?>">

  <div class="grid content-container">
    <div class="col-lg-3 col-md-4 col-sm-12">
      <nav class="menu-list-container">
        <ul class="menu-list"></ul>
      </nav>
    </div>
    <div class="col-lg-9 col-md-8 col-sm-12">
      <div class="">
        <h1 class="page-title"><?=$title?></h1>
        <div class="excerpt">
          <?=$excerpt?>
        </div>
        <div class="editable">
          <?=$content?>
        </div>
      </div>
    </div>
  </div>

  <div class="template-partial" data-name="menu-item">
    <li class="menu-item">
      <div class="menu-item-container">
        <a href="" class="menu-item-link"></a>
        <button href="#" class="dropdown-button">Expand</button>
      </div>
      <ul class="children-list">
      </ul>
    </li>
  </div>

  <div class="template-partial" data-name="child-item">
    <li class="child-item">
      <a href="" class="child-item-link"></a>
    </li>
  </div>
</div>
