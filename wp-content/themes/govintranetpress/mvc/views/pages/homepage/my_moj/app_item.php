<?php if (!defined('ABSPATH')) die(); ?>

<li class="app-item">
  <a href="<?=$url?>" <?=$external ? 'rel="external" target="_blank"' : ''?> class="app-link">
    <span class="app-icon <?=$icon?>-icon"></span>
    <span class="app-name"><?=$title?></span>
  </a>
</li>
