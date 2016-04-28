<?php if (!defined('ABSPATH')) die(); ?>

<h2>Choose your agency or body</h2>

<form class="select-agency-form">
  <ul class="agency-list">
    <?php foreach($agencies as $agency): ?>
      <li class="agency-item" data-agency="<?=$agency['name']?>">
        <a href="#">
          <span class="icon"></span>
          <span class="label"><?=$agency['label']?></span>
        </a>
      </li>
    <?php endforeach ?>
  </ul>

  <input type="submit" class="cta cta-positive submit" value="Done" />
</form>
