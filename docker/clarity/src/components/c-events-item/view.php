<?php

$time_header = "Time:";
if (empty($data['all_day']))
{
    if ($data['multiday'] == 1)
    {
        $times = date("j F Y", strtotime($data['start_date'])) ." - ".date("j F Y", strtotime($data['end_date']));
        $time_header = 'Date:';
    }
    else
        $times = $data['start_time']." - ".$data['end_time'];
} else
    $times = 'All day';


?>
<article class="c-events-item">
    <?php get_component('c-calendar-icon', $data['start_date']); ?>
    <h1><a href="<?php echo $data['url'];?>"><?php echo $data['title'];?></a></h1>
    <div class="c-events-item__time">
      <h2><?php echo $time_header;?></h2>
      <time><?php echo $times;?></time>
    </div>
    <?php if (isset($data['location'])) {?>
    <div class="c-events-item__location">
      <h2>Location:</h2>
      <address><?php echo $data['location'];?></address>
    </div>
    <?php } ?>
</article>
