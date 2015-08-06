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
      this.$myMoj = this.$top.find('.my-moj');
      this.$appsContainer = this.$myMoj.find('.apps-container');
      this.$quickLinksContainer = this.$myMoj.find('.quick-links-container');
      this.$searchForm = this.$top.find('.search-form');
    },

    bindEvents: function() {
      this.$menuButton.on('click', $.proxy(this.toggleMenu, this));
      this.$searchButton.on('click', $.proxy(this.searchClick, this));
      //this.$searchInput.on('blur', $.proxy(this.toggleSearch, this, false));
      $(document).on('click', $.proxy(this.outsideSearchClick, this));
      this.$appsContainer.on('click', '.category-name', $.proxy(this.collapsibleBlockToggle, this));
      this.$quickLinksContainer.on('click', '.category-name', $.proxy(this.collapsibleBlockToggle, this));
    },

    toggleMenu: function(e) {
      this.$top.toggleClass(this.config.menuToggleClass);
    },

    searchClick: function(e) {

      // The behaviour of the search button depends on the window width
      // as detected by the media query:
      // if narrow window, pressing the button opens the search field
      // if wide window, it submits the search

      // check if we're on narrow or wide window through the icon's colour -- not great
      if (window.getComputedStyle(this.$searchButton[0], null).getPropertyValue('background-color') === 'rgb(11, 12, 12)') {
        if(!this.$top.hasClass(this.config.searchToggleClass)) {
          e.preventDefault();
          this.toggleSearch(true);
          this.$searchInput.focus();
        }
      } else {
       this.$searchForm.submit();
      }
    },

    toggleSearch: function(toggleState) {
      this.$top.toggleClass(this.config.searchToggleClass, toggleState);
    },

    outsideSearchClick: function(e) {
      if(!$(e.target).closest('.search-form').length) {
        this.toggleSearch(false);
      }
    },

    collapsibleBlockToggle: function(e) {
      var $this = $(e.target);
      var $container = $(e.delegateTarget);

      $container.toggleClass('mobile-collapsed');
    }
  };
}(window.jQuery));
