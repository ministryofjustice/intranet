(function($) {
  "use strict";

  var App = window.App;

  App.MyMoj = function() {
    if($('head').hasClass('template-home')) { return; }
    this.init();
    this.isOpen = false;
  };

  App.MyMoj.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.setUpMyMoj();
    },

    cacheEls: function() {
      this.$mainMenu = $('.header-menu .categories-list');
      this.$myMoj = $($('[data-name="header-my-moj"]').html());
    },

    bindEvents: function() {
      $(document).on('click', $.proxy(this.outsideClickHandle, this));
    },

    setUpMyMoj: function() {
      this.$mainMenu.append(this.$myMoj);
      this.$myMoj.addClass();

      //cache more elements
      this.$arrow = this.$mainMenu.find('.header-my-moj .arrow');
      this.$menuLink = this.$mainMenu.find('.header-my-moj .category-link');

      //bind more events
      this.$menuLink.on('click', $.proxy(this.toggle, this));
    },

    outsideClickHandle: function(e) {
      console.log($(e.target), $(e.target).closest(this.$myMoj).length);
      if(!$(e.target).closest(this.$myMoj).length) {
        this.close();
      }
    },

    toggle: function(e) {
      e.preventDefault();

      if (this.isOpen) {
        this.close();
      }
      else {
        this.open();
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
    }
  };
}(jQuery));
