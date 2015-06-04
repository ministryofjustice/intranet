/** Sticky news
 */
(function($) {
  "use strict";

  var App = window.App;

  App.StickyNews = function() {
    this.$top = $('.news-widget.need-to-know');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.StickyNews.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.showItem(true);
    },

    cacheEls: function() {
      this.$pages = this.$top.find('.news-item');
      this.$pageLinks = this.$top.find('.page');
    },

    bindEvents: function() {
      this.$pageLinks.on('click', 'a', $.proxy(this.showItem, this, false));
    },

    showItem: function(showFirst, e) {
      var pageId;

      if(e) {
        e.preventDefault();
      }

      if(showFirst) {
        pageId = this.$pageLinks.first().attr('data-page-id');
      }
      else {
        pageId = $(e.target).closest('.page').attr('data-page-id');
      }

      this.$pages.hide();
      this.$pageLinks.removeClass('selected');
      this.$pages.filter('[data-page="'+pageId+'"]').show();
      this.$pageLinks.filter('[data-page-id="'+pageId+'"]').addClass('selected');
    }
  };
}(window.jQuery));
