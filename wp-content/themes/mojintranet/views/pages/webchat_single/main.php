<?php if (!defined('ABSPATH')) die(); ?>
<div class="template-container"
     data-page-id="<?=$id?>">

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
        <!--[if lte IE 8]>
          <?php $this->view('pages/webchat_single/ie_message') ?>
        <![endif]-->
        <div class="coveritlive">
          <?php if($coveritlive_id): ?>
            <?php $this->view('pages/webchat_single/coveritlive_script', array('coveritlive_id' => $coveritlive_id)) ?>
          <?php endif ?>
        </div>
      </div>
    </div>
  </div>

  <?php $this->view('modules/side_navigation') ?>
</div>
