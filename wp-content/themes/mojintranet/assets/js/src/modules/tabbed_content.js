/** Tabbed content
 */
(function($) {
  "use strict";

  var App = window.App;

  App.TabbedContent = function() {
    this.$tabs = $('.content-tabs li');
    if(!this.$tabs.length) { return; }
    this.init();
  };

  App.TabbedContent.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.cacheTemplates();
      this.$tabs.eq(0).click();
    },

    cacheEls: function() {
      this.$tabContent = $('.tab-content');
    },

    cacheTemplates: function() {
      var _this = this;

      this.templates = [];

      $('.template-partial[data-template-type]').each(function() {
        var $el = $(this);
        _this.templates[$el.attr('data-content-name')] = $el.html();
      });
    },

    bindEvents: function() {
      this.$tabs.on('click', $.proxy(this.switchTab, this));
    },

    switchTab: function(e) {
      var $el = $(e.currentTarget);
      var contentName = $el.attr('data-content');
      this.$tabContent.html(this.templates[contentName]);
      e.preventDefault();
      this.setActiveTab($el);

      //hopefully one day we can replace this manual call with Mutation Observer
      App.ins.tableOfContents.generate();
    },

    setActiveTab: function($el) {
      this.$tabs.removeClass('current-menu-item');
      this.$tabs.find('a').attr('aria-selected', false);
      $el.addClass('current-menu-item');
      $el.find('a').attr('aria-selected', true);
    }
  };
}(jQuery));
