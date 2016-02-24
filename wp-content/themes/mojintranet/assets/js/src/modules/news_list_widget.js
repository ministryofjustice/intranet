(function($) {
  "use strict";

  var App = window.App;

  App.NewsListWidget = function() {
    this.$top = $('.template-home .news-list-widget');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.NewsListWidget.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.templateUri = $('head').data('template-uri');
      this.serviceUrl = this.applicationUrl + '/service/news////1/8';
      this.pageBase = this.applicationUrl + '/' + this.$top.data('top-level-slug');
      this.genericThumbnailPath = this.templateUri + '/assets/images/blog-placeholder.jpg';

      this.itemTemplate = this.$top.find('[data-name="widget-news-list-item"]').html();

      this.resultsLoaded = true;
      this.serviceXHR = null;

      this.cacheEls();
      this.bindEvents();

      this.requestNews();
    },

    cacheEls: function() {
      this.$newsList = this.$top.find('.news-list');
    },

    bindEvents: function() {
    },

    requestNews: function() {
      var _this = this;

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl, $.proxy(_this.displayNews, _this));
      //**/}, 2000);
    },

    displayNews: function(data) {
      var _this = this;
      var $newsItem;

      $.each(data.results, function(index, result) {
        $newsItem = _this.buildResultRow(result);
        _this.$newsList.append($newsItem);
      });

      this.resultsLoaded = true;
      this.$top.removeClass('loading');
    },

    buildResultRow: function(data) {
      var $child = $(this.itemTemplate);
      var date = App.tools.parseDate(data.timestamp);

      if(!data.thumbnail_url) {
        data.thumbnail_url = this.genericThumbnailPath;
        data.thumbnail_alt_text = 'generic blog thumbnail';
      }

      $child.find('.news-thumbnail').attr('href', data.url);
      $child.find('.news-thumbnail').attr('src', data.thumbnail_url);
      $child.find('.news-link').attr('alt', data.thumbnail_alt_text);
      $child.find('.news-heading .news-link').html(data.title);
      $child.find('.news-date').html(App.tools.formatDate(date, true));

      return $child;
    }
  };
}(jQuery));
