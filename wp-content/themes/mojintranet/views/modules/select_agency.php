<?php if (!defined('ABSPATH')) die(); ?>

<h2>Choose your agency or body</h2>

<form class="select-agency-form">
  <ul class="agency-list"></ul>

  <script class="template-partial" data-name="select-agency-item" type="text/x-partial-template">
    <li class="agency-item">
      <a href="#">
        <span class="icon"></span>
        <span class="label"></span>
      </a>
    </li>
  </script>
</form>
