(function($) {
  "use strict";

  window.App = {
    tools: {},
    ins: {}
  };

  App.ie = null;

  (function() {
    var $html = $('.html');

    if($html.hasClass('ie7')) {
      App.ie = 7;
    }
    else if($html.hasClass('ie8')) {
      App.ie = 8;
    }
    else if($html.hasClass('ie9')) {
      App.ie = 9;
    }
  }());

  App.ie = 7; //for debugging purposes
}(jQuery));
