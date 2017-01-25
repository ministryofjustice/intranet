(function($) {
  "use strict";

  var App = window.App;

  App.FeaturedNewsWidget = function(data) {
    this.data = data;
    this.$top = $('.template-home .featured-news-widget');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.FeaturedNewsWidget.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.templateUri = $('head').data('template-uri');
      this.pageBase = this.applicationUrl + '/' + this.$top.data('top-level-slug');
      this.genericThumbnailPath = this.templateUri + '/assets/images/news-placeholder.jpg';

      this.itemTemplate = this.$top.find('[data-name="widget-featured-news-item"]').html();

      this.resultsLoaded = false;
      this.serviceXHR = null;

      this.cacheEls();

      this.displayNews(this.data);
    },

    cacheEls: function() {
      this.$newsList = this.$top.find('.news-list');
    },

    displayNews: function(data) {
      var _this = this;
      var $newsItem;

      App.ins.skeletonScreens.remove(this.$newsList);

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
      var author = data.authors[0];

      if(!data.thumbnail_url) {
        data.thumbnail_url = this.genericThumbnailPath;
        data.thumbnail_alt_text = 'generic blog thumbnail';
      }

      $child.attr('data-type', data.post_type);
      $child.find('.news-thumbnail').attr('src', data.thumbnail_url);
      $child.find('.news-thumbnail').attr('alt', data.thumbnail_alt_text);
      $child.find('.news-link').attr('href', data.url);
      $child.find('.title .news-link').html(data.title);
      $child.find('.news-excerpt').html(data.excerpt);
      $child.find('.date').html(App.tools.formatDate(date, true));
      $child.find('.author').html(author.name);

      if (data.post_type !== 'post') {
        $child.find('.meta-separator').addClass('hidden');
        $child.find('.author').addClass('hidden');

        if (data.post_type !== 'news') {
          $child.find('.date').addClass('hidden');
        }
      }

      return $child;
    },

    buildSkeleton: function() {
      var $child = $(this.itemSkeletonTemplate);

      return $child;
    }
  };
}(jQuery));
