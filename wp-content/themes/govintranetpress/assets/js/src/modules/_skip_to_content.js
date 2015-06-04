(function($) {
  "use strict";

  var App = window.App;

  App.SkipToContent = function() {
    this.$top = $('.skip-to-content-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.SkipToContent.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
    },

    bindEvents: function() {
      this.$top.on('focus', 'a', $.proxy(this.toggle, this, true));
      this.$top.on('blur', 'a', $.proxy(this.toggle, this, false));
    },

    toggle: function(toggleState) {
      this.$top.toggleClass('focused', toggleState);
    }
  };
}(jQuery));
