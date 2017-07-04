<?php
namespace MOJ\Intranet;

/**
 * Retrieves Homepage banners
 * Author: Irune Itoiz
 */

class HomepageBanners
{

    /**
     *
     * Return the data for the emergency alert on a per agency basis
     *
     * @param string $agency
     * @return mixed
     */
   public static function getEmergencyBanner($agency = 'hq')
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

    /**
     *
     * Return the data for the sidebar banner on a per agency basis
     * @param string $agency
     * @return array
     */

   public static function getSidebarBanner($agency = 'hq') {

        $data['image_url'] = get_option($agency . '_banner_image_side');
        $data['url'] = get_option($agency . '_banner_link_side');
        $data['alt'] = get_option($agency . '_banner_alt_side');
        $data['text'] = get_option($agency . '_banner_image_side_title');
        $data['visible'] = (int) get_option($agency . '_banner_image_side_enable');

        return $data;
   }


    /**
     *
     * Return the data for the homepage top full width banner on a per agency basis
     * @param string $agency
     * @return array
     *
     */
    public static function getTopBanner($agency = 'hq') {

        $data['image_url'] = get_option($agency . '_banner_image');
        $data['url'] = get_option($agency . '_banner_link');
        $data['alt'] = get_option($agency . '_banner_alt');
        $data['visible'] = (int) get_option($agency . '_banner_image_enable');

        return $data;
    }
}
