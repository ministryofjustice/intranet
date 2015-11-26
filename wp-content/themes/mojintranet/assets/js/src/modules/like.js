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
      this.cacheEls();
      this.bindEvents();

      this.$likeContainer.addClass('loaded');
    },

    cacheEls: function() {
      this.$likeContainer = this.$top.find('.like-container');
      this.$likeCount = this.$top.find('.like-count');
      this.$likeSummary = this.$top.find('.like-summary');
      this.$likeLink = this.$top.find('.like-link');
    },

    bindEvents: function() {
      this.$likeLink.on('click', $.proxy(this.likeClick, this));
    },

    likeClick: function(e) {
      e.preventDefault();
      this.$likeContainer.addClass('voted');
      this.$likeSummary.html('123 people liked this post');
    }
  };
}(jQuery));

