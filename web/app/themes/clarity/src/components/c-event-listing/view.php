<?php
/*
*
* This page is for displaying the event item when it appears in a list format.
* Currently on homepage, team events and event archive.
*
*/

if (isset($events)) :
    // Limit events listed on page to one for homepage display
    if (is_front_page()) {
        $events = array_splice($events, 0, 1);
    }

    foreach ($events as $key => $post) :
        $event_id    = $post->ID;
        $post_url    = $post->url;
        $event_title = $post->post_title;
        $start_time  = substr($post->event_start_time, 0, -3); //removing seconds from string
        $end_time    = substr($post->event_end_time, 0, -3); //removing seconds from string
        $start_date  = $post->event_start_date;
        $end_date    = $post->event_end_date;
        $location    = $post->event_location;
        $date        = $post->event_start_date;
        $year        = date('Y', strtotime($start_date));
        $month       = date('M', strtotime($start_date));
        $day         = date('l', strtotime($start_date));
        $all_day     = $post->event_allday;

        if ($all_day === true) {
            $all_day = 'all_day';
        }

        ?>

<!-- c-event-listing starts here -->

<h2 class="o-title o-title--subtitle">Next event</h2>

<section class="c-event-listing">

        <?php
        // If start date and end date seleced are the same, just display first date.
        if ($start_date === $end_date) {
               $multidate = date('d M', strtotime($start_date));
        } else {
             $multidate = date('d M', strtotime($start_date)) . ' - ' . date('d M', strtotime($end_date));
        }
        ?>

  <a class="c-event-listing--title" href="<?php echo $post_url; ?>"><?php echo $event_title; ?></a>

  <div class="c-event-listing--date">
    <span>Date:</span>
    <time datetime="<?php echo $start_date; ?>">
      <?php echo $day . ', ' . $multidate . ' ' . $year; ?>
      </time>  
  </div>

  <article class="c-events-item-byline__team">

    <header>
        <?php
        if (empty($all_day)) {
            if (isset($start_time) || isset($end_time)) {
                $time = $start_time . ' - ' . $end_time;
            } else {
                $time = '';
            }
        } else {
            $time = 'All day';
        }
        ?>

      <div class="c-event-listing--time">
        <span>Time:</span>
        <?php echo $time; ?>
      </div>

        <?php if (isset($location)) : ?>
        <div class="c-event-listing--location">
          <span>Location:</span>
          <address><?php echo $location; ?></address>
        </div>

        <?php endif; ?>
    </header>
  </article>

</section>
<!-- c-event-listing ends here -->

        <?php
    endforeach; // ($event as $key => $post):
    echo '<a href="/events/" class="o-see-all-link">See all events</a>';
else :
    return ''; // there is no event to display.
endif;
