<?php if (!defined('ABSPATH')) {
    die();
} ?>

<div class="grid">
  <div class="col-lg-12 col-md-12 col-sm-12">
  <h3><?php echo get_sub_field('quote_section_title'); ?></h3>
  </div>
    </div>
  <div class="grid article">
  <?php $quotes = get_sub_field('quotes'); ?>
  <?php if ($quotes):
    foreach ($quotes as $quote): ?>
    <div class="col-lg-12 col-md-12 col-sm-12 section">
      <div class="blockquote"><p><?php echo $quote['quote_text'].'<br>'; ?></p>
      <div class="authorTitle"><p><?php echo $quote['quote_author'].'<br>'; ?></p></div></div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
</div>
