/** Tabbed content
 */
(function($) {
  "use strict";

  var App = window.App;

  App.TabbedContent = function() {
    this.$tabs = $('.content-tabs li a');
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
      this.$tabContent = $('.tab-content');
    },

    bindEvents: function() {
      this.$tabs.on('click', $.proxy(this.switchTab, this));
    },

    switchTab: function(e) {
      var $el = $(e.currentTarget);
      e.preventDefault();

      // switch tab
      this.$tabs.parent().removeClass('current-menu-item');
      this.$tabs.attr('aria-selected', 'false');
      $el.parent().addClass('current-menu-item');
      $el.attr('aria-selected', 'true');

      // switch corresponding panel
      $('.template-partial').hide();
      $('#' + $el.attr('id').replace(/^tab-/, 'panel-')).show();

      //hopefully one day we can replace this manual call with Mutation Observer
      App.ins.tableOfContents.generate();
    }
  };
}(jQuery));
