/** Mobile menu
 */
(function($) {
  "use strict";

  var App = window.App;

  App.MobileMenu = function() {
    this.$top = $('.header');
    if(!this.$top.length) { return; }

    this.config = {
      menuToggleClass: 'mobile-menu-enabled'
    };

    this.init();
  };

  App.MobileMenu.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$menuButton = this.$top.find('.mobile-nav button');
    },

    bindEvents: function() {
      this.$top.on('click', 'button', $.proxy(this.toggleMenu, this));
    },

    toggleMenu: function() {
      this.$top.toggleClass(this.config.menuToggleClass);
    }
  };
}(window.jQuery));
