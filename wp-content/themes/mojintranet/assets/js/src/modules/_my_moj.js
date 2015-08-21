(function($) {
  "use strict";

  var App = window.App;

  App.MyMoj = function() {
    this.$top = $('.my-moj-trigger');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.MyMoj.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$myMoj = $('.my-moj');
      this.$arrow = $('.my-moj-trigger .arrow');
    },

    bindEvents: function() {
      this.$top.on('click', $.proxy(this.toggle, this, true));
    },

    toggle: function() {
      this.$myMoj.slideToggle();
      if (this.$arrow.html() === '▼') {
        this.$arrow.html('▲');
      } else {
        this.$arrow.html('▼');
      }
      return false;
    }
  };
}(jQuery));
