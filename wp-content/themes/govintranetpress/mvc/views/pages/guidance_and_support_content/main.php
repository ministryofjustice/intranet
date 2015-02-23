<?php if (!defined('ABSPATH')) die(); ?>
<div class="guidance-and-support-content" data-redirect-url="<?=$redirect_url?>" data-redirect-enabled="<?=$redirect_enabled?>">
  <div class="grid">
    <div class="col-lg-8">
      <h2 class="page-category">Guidance</h2>
      <h1 class="page-title"><?=$title?></h1>

      <ul class="info-list">
        <li>
          <span>Content owner:</span>
          <span><a href="mailto:<?=$author_email?>"><?=$author?></a></span>
        </li>
        <li>
          <span>History:</span>
          <span>Updated <?=$human_date?></span>
        </li>
      </ul>
      <div class="excerpt">
        <?=$excerpt?>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="right-hand-menu">
        <h3>Quick links</h3>
        <ul>
            <?php for($i=1;$i<=$max_links;$i++) { ?>
            <li>
                <a href="<?=esc_attr($link_array[$i]['linkurl'])?>"><?=esc_attr($link_array[$i]['linktext'])?></a>
            </li>
            <?php } ?>
        </ul>
      </div>
    </div>
  </div>

  <div class="grid">
    <div class="col-lg-3">
      &nbsp;
    </div>
    <div class="col-lg-9">
      <ul class="content-tabs">
        <?php for($i=1;$i<=$tab_count;$i++) { ?>
        <li data-content="<?=str_replace(' ','_',preg_replace('/[^\da-z ]/i', '',strtolower(esc_attr($tab_array[$i]['title']))))?>">
          <a href=""><?=esc_attr($tab_array[$i]['title'])?></a>
        </li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <div class="grid content-container">
    <div class="col-lg-3 col-md-4">
      <div class="js-floater context-menu" data-floater-limiter-selector=".content-container">
        <h4>Contents</h4>
        <ul class="table-of-contents" data-content-selector=".tab-content">
        <?php for($i=1;$i<=$tab_count;$i++) { ?>
          <li>
            <a href="#<?=str_replace(' ','_',preg_replace('/[^\da-z ]/i', '',strtolower(esc_attr($tab_array[$i]['title']))))?>"><?=esc_attr($tab_array[$i]['title'])?></a>
          </li>
        <?php } ?>
        </ul>
      </div>
      &nbsp;
    </div>
    <div class="col-lg-9 col-md-8">
      <div class="tab-content editable">
      </div>
    </div>
  </div>

  <?php for($i=1;$i<=$tab_count;$i++) { ?>
  <div class="template-partial" data-template-type="tab-content" data-content-name="<?=str_replace(' ','_',preg_replace('/[^\da-z ]/i', '',strtolower(esc_attr($tab_array[$i]['title']))))?>">
    <?php for($j=1;$j<=count($tab_array[$i]['sections']);$j++) { ?>
    <h2><?=$tab_array[$i]['sections'][$j]['title']?></h2>
    <?=wpautop($tab_array[$i]['sections'][$j]['content'] )?>
    <?php } ?>
  </div>
<?php } ?>
</div>


