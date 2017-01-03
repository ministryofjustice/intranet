(function($) {
  "use strict";

  var App = window.App;

  App.PostsWidget = function(data) {
    this.data = data;
    this.$top = $('.posts-widget');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.PostsWidget.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.templateUri = $('head').data('template-uri');
      this.pageBase = this.applicationUrl + '/' + this.$top.data('top-level-slug');
      this.postsType = this.$top.attr('data-posts-type') || 'global';
      this.genericThumbnailPath = this.templateUri + '/assets/images/blog-placeholder.jpg';
      this.itemTemplate = this.$top.find('[data-name="widget-post-item"]').html();
      this.resultsLoaded = true;
      this.posts = [];

      this.cacheEls();
      this.bindEvents();
      this.displayPosts(this.data);
    },

    cacheEls: function() {
      this.$postsList = this.$top.find('.posts-list');
    },

    bindEvents: function() {
      $(window).on('breakpoint-change', $.proxy(this.arrangePosts, this));
    },

    displayPosts: function(data) {
      var _this = this;
      var $post;

      App.ins.skeletonScreens.remove(this.$postsList);

      $.each(data.results, function (index, result) {
        _this.posts.push(_this.buildResultRow(index, result));
      });

      if (this.posts.length > 0) {
        this.arrangePosts();
      }
      else {
        this.$top.find('.no-posts-message').addClass('visible');
        this.$top.addClass('no-posts');
      }

      this.resultsLoaded = true;
      this.$top.removeClass('loading');
    },

    buildResultRow: function(index, data) {
      var $child = $(this.itemTemplate);
      var date = App.tools.parseDate(data.timestamp);
      var author = data.authors[0];
      var author_thumbnail = author.thumbnail_url || this.genericThumbnailPath;
      var author_thumbnail_alt_text = author.thumbnail_alt_text || '';

      $child.find('.post-thumbnail').attr('src', author_thumbnail);
      $child.find('.post-thumbnail').attr('alt', author_thumbnail_alt_text);
      $child.find('.post-title .post-link').attr('href', data.url);
      $child.find('.post-title .post-link').html(data.title);
      $child.find('.post-link, .post-link-thumbnail').attr('href', data.url);
      $child.find('.post-date').html(App.tools.formatDate(date, true));
      $child.find('.post-author').html(author.name);

      return $child;
    },

    /**
     * Arranges posts into one or two columns
     */
    arrangePosts: function() {
      var _this = this;
      var column = 1;
      var maxColumns = ($('html').hasClass('breakpoint-desktop') && this.postsType === 'campaign') ? 2 : 1;

      this.$postsList.find('.results-item').detach();

      $.each(this.posts, function (index, $postItem) {
        _this.$postsList.eq(column - 1).append($postItem);

        column++;

        if (column > maxColumns) {
          column = 1;
        }
      });
    }
  };
}(jQuery));
