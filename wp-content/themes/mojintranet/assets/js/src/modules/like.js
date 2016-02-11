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
      this.likesCount = parseInt(this.$top.attr('data-likes-count'), 10);
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

      this.updateLikes(this.likesCount);

      this.$likeContainer.addClass('loaded');
    },

    likeClick: function(e) {
      var _this = this;

      e.preventDefault();

      this.$likeContainer.addClass('voted');
      this.$likeDescription.html('Sending...');

      $.ajax({
        url: this.serviceUrl,
        method: 'put',
        dataType: 'json',
        contentType: 'text',
        success: $.proxy(this.likeSuccess, _this),
        error: $.proxy(this.likeError, _this)
      });
    },

    likeSuccess: function(data) {
      if(!App.tools.search(this.postId, this.likedPostIds)) {
        this.likedPostIds.push(this.postId);
        this.saveLikesToCookie();
        this.updateLikes(data.count);
      }
    },

    updateLikes: function(count) {
      var othersCount = count - 1;
      var message = '';
      var userVoted = App.tools.search(this.postId, this.likedPostIds);

      this.likesCount = count;

      if(count) {
        if(userVoted) {
          if(othersCount) {
            message = 'You and ' + othersCount + ' other ' + (othersCount === 1 ? 'person' : 'people') + ' like this post';
          }
          else {
            message = 'You like this post';
          }
        }
        else {
          message = count + ' ' + (count === 1 ? 'person likes' : 'people like') + ' this post';
        }

        this.$likesRow.removeClass('hidden');
      }

      this.$likeDescription.html(message);
      this.$likeCount.html(count);
    },

    likeError: function() {
      this.$likeDescription.html('There was a problem saving your like. Please try again later');
    }
  };
}(jQuery));
