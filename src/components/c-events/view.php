<!-- c-events starts here -->
<?php
use MOJ\Intranet\Events;

if (!defined('ABSPATH')) {
    die();
}

$oEvents = new Events();

$eventsList = $oEvents->getEvents();
$post_id = get_the_ID();

foreach ($eventsList as $event) {
    if ($post_id == $event['id']) {
        $start_time = $event['start_time'];
        $end_time = $event['end_time'];
        $all_day = $event['all_day'];
        $location = $event['location'];
    }
}
?>
<article class="c-events">
  <header>

    <?php get_template_part('src/components/c-calendar-icon/view', 'event'); ?>

    <?php
    // Set time to either 'all day' or display the time selected.
    if (empty($all_day)) {
        if (isset($start_time) || isset($end_time)) {
            $time = $start_time . " - " . $end_time;
        } else {
            $time = '';
        }
    } else {
        $time = 'All day';
    }
    ?>

    <div class="c-events__time">
      <h2>Time:</h2>
      <?php echo $time; ?>
    </div>
    <?php if (isset($location)): ?>
      <div class="c-events__location">
        <h2>Location:</h2>
        <address><?php echo $location; ?></address>
      </div>
    <?php endif; ?>

  </header>
</article>

<!-- c-events ends here -->
