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
      this.serviceUrl = this.applicationUrl + '/service/widgets/non-featured-news/' + App.tools.helpers.agency.getForContent() + '//0/8';
      this.pageBase = this.applicationUrl + '/' + this.$top.data('top-level-slug');
      this.genericThumbnailPath = this.templateUri + '/assets/images/news-placeholder.jpg';

      this.itemTemplate = this.$top.find('[data-name="widget-news-list-item"]').html();

      this.resultsLoaded = false;
      this.serviceXHR = null;

      this.news = [];

      this.cacheEls();
      this.bindEvents();

      this.requestNews();
    },

    cacheEls: function() {
      this.$newsList = this.$top.find('.news-list');
    },

    bindEvents: function() {
      $(window).on('breakpoint-change', $.proxy(this.displayNews, this));
    },

    requestNews: function() {
      var _this = this;

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl, $.proxy(_this.buildNewsRows, _this));
      //**/}, 2000);
    },

    buildNewsRows: function(data) {
      var _this = this;

      App.ins.skeletonScreens.remove(this.$newsList);

      $.each(data.results, function(index, result) {
        _this.news.push(_this.buildResultRow(result));
      });

      if (this.news.length > 0) {
        this.displayNews();
      }
      else {
        this.$top.find('.no-news-message').addClass('visible');
        this.$top.addClass('no-news');
      }

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

      $child.find('.news-thumbnail').attr('src', data.thumbnail_url);
      $child.find('.news-thumbnail').attr('alt', data.thumbnail_alt_text);
      $child.find('.news-link').attr('href', data.url);
      $child.find('.title .news-link').html(data.title);
      $child.find('.date').html(App.tools.formatDate(date, true));

      return $child;
    },

    displayNews: function() {
      var _this = this;
      var column = 1;
      var maxColumns = ($('html').hasClass('breakpoint-desktop')) ? 2 : 1;

      this.$newsList.find('.news-item').detach();

      $.each(this.news, function (index, $newsItem) {
        _this.$newsList.eq(column - 1).append($newsItem);

        column++;

        if (column > maxColumns) {
          column = 1;
        }
      });
    }
  };
}(jQuery));
