/** Emergency message
 */
(function($) {
  "use strict";

  var App = window.App;

  App.EmergencyMessage = function() {
    this.$top = $('.message');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.EmergencyMessage.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$closeButton = this.$top.find('.close');
    },

    bindEvents: function() {
      this.$closeButton.on('click', $.proxy(this.close, this));
    },

    close: function() {
      this.$top.slideUp(200);
    }
  };
}(window.jQuery));
