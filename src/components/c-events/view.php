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
    <?php get_component('c-calendar-icon', $data['start_date']); ?>

      <?php if (isset($data['start_date']) || isset($data['end_date'])): ?>
      <?php $date = date("j F Y", strtotime($data['start_date'])) ." - ".date("j F Y", strtotime($data['end_date'])); ?>
      <div class="c-events__date">
        <h2>Date:</h2>
        <time><?php echo $date;?></time>
      </div>
      <?php endif; ?>

      <?php if (isset($data['location'])): ?>
        <div class="c-events__location">
          <h2>Location:</h2>
          <address><?php echo $data['location']; ?></address>
        </div>
      <?php endif; ?>

      <?php
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

  </header>
</article>

<!-- c-events ends here -->
