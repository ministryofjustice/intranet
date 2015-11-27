/** Like
 */
(function($) {
  "use strict";

  var App = window.App;

  App.Like = function() {
    this.$top = $('.template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.Like.prototype = {
    init: function() {
      this.settings = {
        cookieName: "dw_like",
        cookieLifetime: 365
      };

      this.postId = this.$top.attr('data-post-id');
      this.nonce = this.$top.attr('data-nonce');
      this.likesCount = this.$top.attr('data-likes-count');
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/likes/' + this.postId;
      this.likedPostIds = this.getLikesFromCookie();

      this.cacheEls();
      this.bindEvents();

      this.initializeLikeButton();
    },

    cacheEls: function() {
      this.$likeContainer = this.$top.find('.like-container');
      this.$likeCount = this.$top.find('.like-count');
      this.$likeSummary = this.$top.find('.like-summary');
      this.$likeDescription = this.$top.find('.like-description');
      this.$likeLink = this.$top.find('.like-link');
      this.$likesRow = this.$top.find('.likes-row');
    },

    bindEvents: function() {
      this.$likeLink.on('click', $.proxy(this.likeClick, this));
    },

    getLikesFromCookie: function() {
      var likesString = App.tools.getCookie(this.settings.cookieName);
      var likes = [];

      if(likesString) {
        likes = JSON.parse(likesString);
      }

      return likes;
    },

    saveLikesToCookie: function() {
      App.tools.setCookie(this.settings.cookieName, JSON.stringify(this.likedPostIds), this.settings.cookieLifetime);
    },

    initializeLikeButton: function() {
      if(App.tools.search(this.postId, this.likedPostIds)) {
        this.$likeContainer.addClass('voted');
      }

      this.updateLikes({count: this.likesCount});
      this.$likeContainer.addClass('loaded');
    },

    likeClick: function(e) {
      var _this = this;

      e.preventDefault();

      this.$likeContainer.addClass('voted');
      this.$likeDescription.html('Sending...');

      $.ajax({
        url: this.serviceUrl,
        method: 'post',
        dataType: 'json',
        data: {
          nonce: this.nonce
        },
        success: $.proxy(this.updateLikes, _this),
        error: $.proxy(this.likeError, _this)
      });
    },

    updateLikes: function(data) {
      this.likesCount = data.count;
      var othersCount = data.count - 1;
      var message;

      if(othersCount) {
        message = 'You and ' + othersCount + ' ' + (othersCount === 1 ? 'person likes' : 'people like') + ' this post';
      }
      else {
        message = 'You like this post';
      }

      this.$likeDescription.html(message);
      this.$likeCount.html(data.count);

      if(this.likesCount > 0) {
        this.$likesRow.removeClass('hidden');
      }

      if(!App.tools.search(this.postId, this.likedPostIds)) {
        this.likedPostIds.push(this.postId);
        this.saveLikesToCookie();
      }
    },

    likeError: function() {
      this.$likeDescription.html('There was a problem saving your like. Please try again later');
    }
  };
}(jQuery));
