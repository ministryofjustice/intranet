/** Mobile header
 */
(function($) {
  "use strict";

  var App = window.App;

  App.MobileHeader = function() {
    this.$top = $('.header');
    if(!this.$top.length) { return; }

    this.config = {
      menuToggleClass: 'menu-opened',
      searchToggleClass: 'search-opened'
    };

    this.init();
  };

  App.MobileHeader.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$searchInput = this.$top.find('.keywords-field');
      this.$searchButton = this.$top.find('.search-btn');
      this.$menuButton = this.$top.find('.mobile-menu-btn');
    },

    bindEvents: function() {
      this.$menuButton.on('click', $.proxy(this.toggleMenu, this));
      this.$searchButton.on('click', $.proxy(this.searchClick, this));
      //this.$searchInput.on('blur', $.proxy(this.toggleSearch, this, false));
      $(document).on('click', $.proxy(this.outsideSearchClick, this));
    },

    toggleMenu: function(e) {
      this.$top.toggleClass(this.config.menuToggleClass);
    },

    searchClick: function(e) {
      if(!this.$top.hasClass(this.config.searchToggleClass)) {
        e.preventDefault();
        this.toggleSearch(true);
        this.$searchInput.focus();
      }
    },

    toggleSearch: function(toggleState) {
      this.$top.toggleClass(this.config.searchToggleClass, toggleState);
    },

    outsideSearchClick: function(e) {
      if(!$(e.target).closest('.search-form').length) {
        this.toggleSearch(false);
      }
    }
  };
}(window.jQuery));
