<?php if (!defined('ABSPATH')) die();
/* Template name: About us */

/** this class should be renamed to About_us_redirect once the MVC router is created
 */
class About_us extends MVC_controller {
  function main(){
    $dw_agency_cookie = $_COOKIE['dw_agency'];
    header("Location: ".site_url('about-us-'.$dw_agency_cookie));
  }
}
