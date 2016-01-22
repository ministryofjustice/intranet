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

      this.itemTemplate = this.$top.find('[data-name="comment-item"]').html();
      this.serviceXHR = null;

      this.cacheEls();
      this.bindEvents();

      this.loadComments();
    },

    cacheEls: function() {
      this.$commentForm = this.$top.find('.comment-form');
      this.$commentCancelBtn = this.$commentForm.find('.cta.cancel');
      this.$commentField = this.$commentForm.find('[name="comment"]');
      this.$commentsList = this.$top.find('.comments-list');
      this.$commentsCount = this.$top.find('.comments-count');
    },

    bindEvents: function() {
      var _this = this;

      this.$commentField.focus(function() {
        _this.$commentForm.addClass('active');
      });

      this.$commentCancelBtn.click(function() {
        _this.$commentForm.removeClass('active');
      });
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
      var $comment, $reply;

      for(a = 0, totalComments = data.comments.length; a < totalComments; a++) {
        comment = data.comments[a];
        $comment = this.buildComment(comment);
        this.$commentsList.append($comment);

        for(b = 0, totalReplies = comment.replies.length; b < totalReplies; b++) {
          reply = comment.replies[b];
          $reply = this.buildComment(reply);
          $comment.find('> .replies-list').append($reply);
        }
      }

      this.$commentsCount.find('.count').html(data.total_comments);
    },

    buildComment: function(data) {
      var $comment = $(this.itemTemplate);

      $comment.find('.content').html(data.comment);
      $comment.find('.datetime').html(data.date_posted);
      $comment.find('.author').html(data.author);
      $comment.find('.likes .count').html(data.likes);

      return $comment;
    }
  };
}(jQuery));
