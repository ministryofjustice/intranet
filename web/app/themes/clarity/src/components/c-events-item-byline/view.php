<?php
if (! defined('ABSPATH')) {
    die();
}

?>
<!-- c-events-item-byline starts here -->
<article class="c-events-item-byline">
  <header>

      <h3 class="c-events-item-byline__link"><a href="<?= $post_url ?? '' ?>"><?= $event_title ?? '' ?></a></h3>


    <?php
    if (empty($all_day)) {
        if (isset($start_time) || isset($end_time)) {
            // If start date and end date selected are the same, just display first date.
            if ($start_time === $end_time) {
              $time = substr($start_time, 0, 5);
            } else {
              $time = substr($start_time, 0, 5) . ' - ' . substr($end_time, 0, 5);
            }
        } else {
            $time = '';
        }
    } else {
        $time = 'All day';
        $datetime = 'P1D'; //period 1 day duration
    }
    ?>

    <div class="c-events-item-byline__time">
      <span>Time:</span>
      <time datetime='<?php echo $datetime;?>'>
        <?php echo $time; ?>
      </time>
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
