<?php if (!defined('ABSPATH')) die(); ?>

<h2>Choose your agency or body</h2>

<ul class="agency-list">
  <?php foreach($agencies as $agency): ?>
    <li class="agency-item">
      <a href="#">
        <span class="icon"></span>
        <span class="label"><?=$agency['label']?></span>
      </a>
    </li>
  <?php endforeach ?>
</ul>

<input type="submit" class="cta cta-positive submit" value="Done" />
