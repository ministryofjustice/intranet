/* plugin jQuery */

(function($) {
  //"use strict";

  $(document).ready(function() {
    jQuery("#wp-admin-bar-clarity_connection_status").html('<a class="ab-item">Connection status: <span style="color:#72bd72;">online</span></a>')
  });

  window.addEventListener('offline', function(e) {

    jQuery("#wp-admin-bar-clarity_connection_status").html('<a class="ab-item">Connection status: <span style="color:#ea5a48;">offine</span></a>')

  });

  window.addEventListener('online', function(e) {

    jQuery("#wp-admin-bar-clarity_connection_status").html('<a class="ab-item">Connection status: <span style="color:#72bd72;">online</span></a>')

  });

})(jQuery);
