(function($) {
  "use strict";

  var App = window.App;

  App.InitMediaPlayer = function() {
    this.$top = $('.wp-video');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.InitMediaPlayer.prototype = {
    init: function() {
      App.tools.injectCss('/wp-includes/js/mediaelement/mediaelementplayer.min.css');
      App.tools.injectCss('/wp-includes/js/mediaelement/wp-mediaelement.css');
    }
  };
}(jQuery));
