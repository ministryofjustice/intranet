/** Like
 * Designed to work with multiple instances on the same page
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

      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl + '/service/likes/';
      this.likedPostIds = this.getLikesFromCookie();

      this.initializeLikes();
    },

    bindEvents: function($element) {
      this.getLinkEl($element).click($.proxy(this.likeClick, this));
    },

    getLikesFromCookie: function() {
      var likesString = App.tools.getCookie(this.settings.cookieName);
      var parsedLikes;
      var likes = {
        'comment' : [],
        'post': []
      };

      if(likesString) {
        parsedLikes = JSON.parse(likesString);
        if($.type(parsedLikes) === 'object') {
          likes = parsedLikes;
        }
      }

      return likes;
    },

    saveLikesToCookie: function() {
      App.tools.setCookie(this.settings.cookieName, JSON.stringify(this.likedPostIds), this.settings.cookieLifetime);
    },

    /**
     * This method can be safely run multiple times
     */
    initializeLikes: function() {
      var _this = this;
      var $el;

      this.$top.find('.like-container').not('[data-initialized="1"]').each(function() {
        $el = $(this);
        $el.attr('data-initialized', 1);

        _this.bindEvents($el);

        _this.initializeLikeButton($el);
      });
    },

    initializeLikeButton: function($element) {
      var postId = this.getPostId($element);
      var postType = this.getPostType($element);
      var $container = this.getContainerEl($element);
      var $description = this.getDescriptionEl($element);

      if(App.tools.search(postId, this.likedPostIds[postType])) {
        $container.addClass('voted');
      }

      $description.html($element.attr('data-likes-count'));

      $container.addClass('loaded');
    },

    likeClick: function(e) {
      var _this = this;
      var $element = $(e.target);
      var $container = this.getContainerEl($element);
      var $description = this.getDescriptionEl($element);
      var postType = this.getPostType($element);
      var postId = this.getPostId($element);

      e.preventDefault();

      if($container.hasClass('voted')) {
        return;
      }

      $container.addClass('voted');
      $description.html('Sending...');

      $.ajax({
        url: this.serviceUrl + postType + '/' + postId,
        method: 'put',
        dataType: 'json',
        contentType: 'text',
        success: $.proxy(this.likeSuccess, _this, $element),
        error: $.proxy(this.likeError, _this, $element)
      });
    },

    likeSuccess: function($element, data) {
      var postId = this.getPostId($element);
      var postType = this.getPostType($element);

      if(!App.tools.search(postId, this.likedPostIds[postType])) {
        this.likedPostIds[postType].push(postId);
        this.saveLikesToCookie();
        this.updateLikes(data.count, $element);
      }
    },

    likeError: function($element) {
      //console.log($element);
    },

    updateLikes: function(count, $element) {
      if(count) {
        this.getContainerEl($element).removeClass('hidden');
      }

      this.getDescriptionEl($element).html(count);
    },

    getContainerEl: function($element) {
      return $element.closest('.like-container');
    },

    getDescriptionEl: function($element) {
      return this.getContainerEl($element).find('.like-description');
    },

    getLinkEl: function($element) {
      return $element.find('.like-link');
    },

    getPostType: function($element) {
      return this.getContainerEl($element).attr('data-post-type');
    },

    getPostId: function($element) {
      return this.getContainerEl($element).attr('data-post-id');
    }
  };
}(jQuery));




/** OLD
 */
(function($) {
  "use strict";

  var App = window.App;

  App.Z = function() {
    if(1) { return; }
    this.$top = $('.template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.Z.prototype = {
    init: function() {
      this.settings = {
        cookieName: "dw_like",
        cookieLifetime: 365
      };

      this.postId = this.$top.attr('data-post-id');
      this.likesCount = parseInt(this.$top.attr('data-likes-count'), 10);
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/likes/post/' + this.postId;
      this.likedPostIds = this.getLikesFromCookie();

      this.cacheEls();
      this.bindEvents();

      this.initializeLikeButton();
    },

    cacheEls: function() {
      this.$likesRow = this.$top.find('.likes-row');
      this.$likeCount = this.$top.find('.like-count');
      this.$likeContainer = this.$top.find('.like-container');
      this.$likeLink = this.$top.find('.like-link');
      this.$likeDescription = this.$top.find('.like-description');
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

      if(this.$likeContainer.hasClass('voted')) {
        return;
      }

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
      var message = '';
      message += count + ' ' + (count === 1 ? 'like' : 'likes');

      if(count) {
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
