(function($) {
  "use strict";

  var App = window.App;

  App.MyMoj = function() {
    this.$top = $('.my-moj-trigger');
    if(!this.$top.length) { return; }
    this.init();
    this.isOpen = false;
  };

  App.MyMoj.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$container = $('.my-moj-container');
      this.$myMoj = $('.my-moj');
      this.$arrow = $('.my-moj-trigger .arrow');
    },

    bindEvents: function() {
      this.$top.on('click', $.proxy(this.toggle, this, false));
    },

    close: function(e) {
      this.$myMoj.hide();
      this.$arrow.html('▼');
      this.isOpen = false;
      $('#content').off('click');
    },

    open: function() {
      this.$myMoj.show();
      this.$arrow.html('▲');
      this.isOpen = true;
      $('#content').on('click', $.proxy(this.close, this));
    },

    toggle: function() {
      if (this.isOpen) {
        this.close();
      } else {
        this.open();
      }
      return false;
    }
  };
}(jQuery));
