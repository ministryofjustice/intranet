<!-- c-events starts here -->
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

// FAKE DATA
$times = 'All day'

?>
<article class="c-events">
  <header>
    <h1>Fake title<?php //echo $data['title'];?></h1>
    <?php get_component('c-calendar-icon', $data['start_date']); ?>
      <div class="c-events__time">
        <h2><?php echo $time_header;?></h2>
        <time><?php echo $times;?></time>
      </div>
      <?php //if (isset($data['location'])) {?>
      <div class="c-events__location">
        <h2>Location:</h2>
        <address>Sage Gateshead<?php //echo $data['location'];?></address>
      </div>
      <?php //} ?>
  </header>

  <div class="c-events__details">
    <p>Civil Service Live is the government’s annual, cross-department learning event, attracting thousands of civil servants to regional events to learn, network and share best practice. The conferences offer engaging, interactive and thought provoking sessions, led by dynamic, expert and inspirational speakers. Senior leaders from the Civil Service, Parliament, the public and private sector will be talking about their experiences, sharing their knowledge, and taking questions.</p>
    <p>You can book your place when registration opens in late April.</p>
  </div>
  <a href="#" class="o-share-link">Share event by email</a>
</article>

<!-- c-events ends here -->
