/** Sticky news
 */
(function($) {
  "use strict";

  var App = window.App;

  App.StickyNews = function() {
    this.$top = $('#need-to-know');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.StickyNews.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.showItem(1);
    },

    cacheEls: function() {
      this.$pages = this.$top.find('.need-to-know-list > li');
      this.$pageLinks = this.$top.find('.page-list > li');
    },

    bindEvents: function() {
      this.$pageLinks.on('click', $.proxy(this.showItem, this, null));
    },

    showItem: function(pageId, e) {
      if(!pageId) {
        pageId = $(e.target).data('page-id');
      }

      this.$pages.hide();
      this.$pageLinks.removeClass('selected');
      this.$pages.filter('[data-page="'+pageId+'"]').show();
      this.$pageLinks.filter('[data-page-id="'+pageId+'"]').addClass('selected');
    }
  };
}(window.jQuery));
