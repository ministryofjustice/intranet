<?php if (!defined('ABSPATH')) {
    die();
}
?>

<?php
  $feature_video = get_sub_field('feature_video');
  $feature_image = get_sub_field('feature_image');
?>

<div class="grid content-container">
  <div class="col-lg-8 col-md-8 col-sm-12">

    <?php if ($feature_image): ?>
      <img width="600" height="400" src="<?php echo $feature_image; ?>" />
    <?php endif; ?>

    <!--[if !IE]><!-->
      <?php if ($feature_video): ?>
          <?php echo '<iframe width="600" height="338" src="https://www.youtube.com/embed/'. $feature_video . '?wmode=transparent' .'" data-state="" frameborder="0"></iframe>' ?>
      <?php endif; ?>
      <!--<![endif]-->

    <!--[if lte IE 9]>
      <?php if ($feature_video): ?>
          <?php echo '<iframe width="600" height="338" src="https://www.youtube.com/v/'.
          $feature_video . '?wmode=transparent' .'" data-state="" frameborder="0"></iframe>' ?>
      <?php endif; ?>
    <![endif]-->

  </div>
  <div class="col-lg-4 col-md-4 col-sm-12 editable">
    <p>
    <?php if ($feature_image || $feature_video) {
    echo get_sub_field('feature_text');
} ?>
    </p>
  </div>
</div>
