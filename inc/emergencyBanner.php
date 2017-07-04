<?php
namespace MOJ\Intranet;

/**
 * Retrieves News related data
 * Author: Irune Itoiz
 */

class EmergencyBanner
{
   static function getEmergencyBanner($agency = 'hq')
   {
       $message = get_option($agency . '_homepage_control_emergency_message');
       $type = get_option($agency . '_emergency_type');

       $data['visible'] = (int) get_option($agency . '_emergency_toggle');
       $data['title'] = get_option($agency . '_emergency_title');
       $data['date'] = get_option($agency . '_emergency_date');
       $data['message'] = apply_filters('the_content', $message, true);
       $data['type'] = !$type ? 'emergency' : $type;

       return $data;
   }

}
