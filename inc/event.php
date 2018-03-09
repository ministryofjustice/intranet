<?php
namespace MOJ\Intranet;

if (!defined('ABSPATH')) {
    die();
}

/*
*
* This class generates an event item in an array for use on all calendar and event components.
*
*/
class Event
{
    public function getEventItem($post)
    {
        $id = $post->ID;

        $start_date = get_post_meta($id, '_event-start-date', true);
        $end_date = get_post_meta($id, '_event-end-date', true);

        return [
            'id' => $id,
            'title' => (string) get_the_title($id),
            'url' => (string) get_the_permalink($id),
            'slug' => (string) $post->post_name,
            'location' => (string) get_post_meta($id, '_event-location', true),
            'description' => (string) get_the_content_by_id($id),
            'start_date' => (string) $start_date,
            'start_time' => (string) get_post_meta($id, '_event-start-time', true),
            'end_date' => (string) $end_date,
            'end_time' => (string) get_post_meta($id, '_event-end-time', true),
            'all_day' =>  get_post_meta($id, '_event-allday', true) == true,
            'multiday' => (string) $start_date !== $end_date
      ];
    }
}
