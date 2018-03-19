<?php if (!defined('ABSPATH')) die(); ?>

<ul class="content-panels">
  <?php foreach($tabs as $tab): ?>
    <li
      id="panel-<?=$tab['name']?>"
      data-content-name="<?=$tab['name']?>"
      class="tab-content editable <?=$tab['hidden_class']?>"
      role="tabpanel"
      aria-labelled-by="tab-<?='tab-'.$tab['name']?>"
    >
      <?php foreach($tab['sections'] as $section): ?>
        <h2><?=$section['section_title']?></h2>
        <?=$section['section_html_content']?>
      <?php endforeach ?>

      <?php foreach($tab['link_groups'] as $link_group): ?>
        <h2><?=$link_group['heading'] ?: 'Links'?></h2>
        <ul>
          <?php foreach($link_group['links'] as $link): ?>
            <li>
              <a href="<?=$link['url']?>"><?=$link['title']?></a>
            </li>
          <?php endforeach ?>
        </ul>
      <?php endforeach ?>
    </li>
  <?php endforeach ?>
</ul>
