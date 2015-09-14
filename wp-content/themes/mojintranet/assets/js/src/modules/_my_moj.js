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
      $(document).on('click', $.proxy(this.outsideClickHandle, this));
    },

    outsideClickHandle: function(e) {
      if(!$(e.target).closest(this.$myMoj).length) {
        this.close();
      }
    },

    close: function(e) {
      this.$myMoj.removeClass('visible');
      this.$arrow.html('▼');
      this.isOpen = false;
    },

    open: function() {
      this.$myMoj.addClass('visible');
      this.$arrow.html('▲');
      this.isOpen = true;
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
