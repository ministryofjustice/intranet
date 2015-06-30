<?php if (!defined('ABSPATH')) die(); ?>

<li class="app-item">
  <a href="<?=$url?>" <?=$external ? 'rel="external"' : ''?> class="app-link">
    <span class="app-icon">
      <span class="app-icon-inner <?=$icon?>-icon"></span>
    </span>
    <span class="app-name"><?=$title?></span>
  </a>
</li>
