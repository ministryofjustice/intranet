<!-- c-events starts here -->
<?php
use MOJ\Intranet\Events;

if (!defined('ABSPATH')) {
    die();
}

$oEvents = new Events();

$options = [
    'page' => 1,
    'per_page' => 2
    ];

$eventsList = $oEvents->getEvents($options);
$data = $eventsList[1];
?>
<article class="c-events">
  <header>
    
    <?php get_template_part('src/components/c-calendar-icon/view', 'event'); ?>

    <?php
    // Set time to either 'all day' or display the time selected.
    if (empty($data['all_day'])) {
        if (isset($data['start_time']) || isset($data['end_time'])) {
            $time = $data['start_time']." - ".$data['end_time'];
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
    <?php if (isset($data['location'])): ?>
      <div class="c-events__location">
        <h2>Location:</h2>
        <address><?php echo $data['location']; ?></address>
      </div>
    <?php endif; ?>

  </header>
</article>

<!-- c-events ends here -->
