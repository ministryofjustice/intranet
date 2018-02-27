<!-- c-events starts here -->
<?php

$time_header = "Time:";
if (empty($data['all_day'])) {
    if ($data['multiday'] == 1) {
        $times = date("j F Y", strtotime($data['start_date'])) ." - ".date("j F Y", strtotime($data['end_date']));
        $time_header = 'Date:';
    } else {
        $times = $data['start_time']." - ".$data['end_time'];
    }
} else {
    $times = 'All day';
}
?>

<article class="c-events">
  <header>
    <?php get_component('c-calendar-icon', $data['start_date']); ?>
      <div class="c-events__time">
        <h2><?php echo $time_header;?></h2>
        <time><?php echo $times;?></time>
      </div>
      <?php if (isset($data['location'])) {?>
      <div class="c-events__location">
        <h2>Location:</h2>
        <address><?php echo $data['location'];?></address>
      </div>
      <?php } ?>
  </header>
</article>

<!-- c-events ends here -->
