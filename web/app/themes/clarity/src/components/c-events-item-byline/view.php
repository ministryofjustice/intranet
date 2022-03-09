<?php
if (! defined('ABSPATH')) {
    die();
}

?>
<!-- c-events-item-byline starts here -->
<article class="c-events-item-byline">
  <header>

      <h3 class="c-events-item-byline__link"><a href="<?php echo $post_url; ?>"><?php echo $event_title; ?></a></h3>


    <?php
    if (empty($all_day)) {
        if (isset($start_time) || isset($end_time)) {
            $time = substr($start_time, 0, 5) . ' - ' . substr($end_time, 0, 5);
        } else {
            $time = '';
        }
    } else {
        $time = 'All day';
    }
    ?>

    <div class="c-events-item-byline__time">
      <span>Time:</span>
      <?php echo $time; ?>
    </div>

    <?php if (isset($location)) : ?>
      <div class="c-events-item-byline__location">
        <span>Location:</span>
        <address><?php echo $location; ?></address>
      </div>

    <?php endif; ?>
  </header>
</article>
<!-- c-events-item-byline ends here -->
