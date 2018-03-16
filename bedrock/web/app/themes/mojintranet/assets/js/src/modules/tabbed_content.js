/** Tabbed content
 */
(function($) {
  "use strict";

  var App = window.App;

  App.TabbedContent = function() {
    this.$tabs = $('.content-tabs:not(.static) li');
    if(!this.$tabs.length) { return; }
    this.init();
  };

  App.TabbedContent.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();

      this.$tabs.eq(0).click();
    },

    cacheEls: function() {
      this.$contentPanels = $('.content-panels').find('.tab-content');
    },

    bindEvents: function() {
      this.$tabs.on('click', $.proxy(this.switchTab, this));
    },

    switchTab: function(e) {
      e.preventDefault();

      this.setActiveTab($(e.currentTarget));

      //hopefully one day we can replace this manual call with Mutation Observer
      //TODO: Do we still use the table of contents? To be investigated
      App.ins.tableOfContents.generate();
    },

    setActiveTab: function($el) {
      var tabName = $el.attr('data-tab-name');

      this.$tabs.removeClass('current-menu-item');
      this.$tabs.find('a').attr('aria-selected', false);
      this.$contentPanels.addClass('hidden');
      $el.addClass('current-menu-item');
      $el.find('a').attr('aria-selected', true);
      this.$contentPanels.filter('[data-content-name="' + tabName + '"]').removeClass('hidden');
    }
  };
}(jQuery));
