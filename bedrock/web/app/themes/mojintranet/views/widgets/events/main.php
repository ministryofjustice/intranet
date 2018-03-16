<?php if (!defined('ABSPATH')) {
    die();
} ?>
<?php

  global $post;

  $terms = get_the_terms($post->ID, 'campaign_category');

  foreach ($terms as $term) {
      $campaign_id = $term->term_id;
  }
?>
<div class="widget-event-item">
  <h2 class="category-name">Events</h2>
  <div id="content">
    <?php get_events_api('campaign', $campaign_id); ?>
  </div>
</div>
<style>
/* Using Clarity CSS so that we can use the Clarity API. */

  .c-events-item {
      position: relative;
      padding-bottom: 1.5rem;
      display: inline-block;
      width: 100%;
      margin-bottom: 2rem;
  }

  .c-events-item .c-calendar-icon {
    position: absolute;
    width: 25%;
  }

  .c-events-item h1 {
    font-size: 1.4em;
    font-family: nta,sans-serif;
    font-weight: 700;
    margin-bottom: 1rem;
    display: block;
    padding-left: 30%;
  }

  .c-events-item__time h2 {
    font-size: 1em;
    line-height: 0;
    float: left;
    clear: none;
    text-align: inherit;
    width: 33.33333333333333%;
    margin-left: 0;
    margin-right: 0;
    font-weight: 400;
  }

  .c-events-item__time time {
    float: left;
    clear: none;
    text-align: inherit;
    width: 66.66666666666666%;
    margin-left: 0;
    margin-right: 0;
  }

  .c-events-item__location h2 {
      font-size: 0.7em;
      line-height: 0;
      float: left;
      clear: none;
      text-align: inherit;
      width: 33.33333333333333%;
      margin-left: 0;
      margin-right: 0;
      font-weight: 400;
    }

  .c-events-item__time {
    display: block;
    padding-left: 30%;
    clear: both;
  }

  .c-events-item__location {
    display: block;
    padding-left: 30%;
    clear: both;
    font-size: 1.4rem;
  }

  .c-events-item__location address {
    font-size: 0.7em;
    float: left;
    clear: none;
    text-align: inherit;
    width: 66.66666666666666%;
    margin-left: 0;
    margin-right: 0;
  }

  .c-events-item__time address {
    font-size: 0.7em;
    float: left;
    clear: none;
    text-align: inherit;
    width: 66.66666666666666%;
    margin-left: 0;
    margin-right: 0;
  }

  .c-calendar-icon span {
      display: block;
      text-align: center;
      line-height: normal;
  }

  .c-calendar-icon--dow {
    background-color: #ebedef;
    font-size: 1rem;
    padding-top: .2rem;
  }

  .c-calendar-icon--dom {
    font-size: 2.4rem;
    font-weight: 700;
  }

  .c-calendar-icon--my {
      font-size: 1rem;
      padding-bottom: .2rem;
  }

  .u-visually-hidden {
    position: absolute;
    left: -9999px;
    top: -9999px;
    color: transparent;
}
</style>
