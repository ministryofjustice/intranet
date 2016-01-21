(function($) {
  "use strict";

  var App = window.App;

  App.Comments = function() {
    this.$top = $('.template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.Comments.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl + '/wp-content/themes/mojintranet/assets/js/comments.json';

      this.serviceXHR = null;

      this.cacheEls();
      this.bindEvents();

      this.loadComments();
    },

    cacheEls: function() {
    },

    bindEvents: function() {
    },

    loadComments: function() {
      this.requestComments();
    },

    requestComments: function() {
      var _this = this;

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl, $.proxy(_this.displayComments, _this));
      //**/}, 2000);
    },

    displayComments: function(data) {
      var a, b;
      var totalComments, totalReplies;
      var comment, reply;

      for(a = 0, totalComments = data.comments.length; a < totalComments; a++) {
        comment = data.comments[a];
        console.log(comment);

        for(b = 0, totalReplies = comment.replies.length; b < totalReplies; b++) {
          reply = comment.replies[b];
          console.log(reply);
        }
      }
    }
  };
}(jQuery));
