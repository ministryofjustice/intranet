(function($) {
  "use strict";

  var App = window.App;

  App.Comments = function() {
    this.$top = $('.comments-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.Comments.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl + '/wp-content/themes/mojintranet/assets/js/comments.json';

      this.itemTemplate = this.$top.find('[data-name="comment-item"]').html();
      this.formTemplate = this.$top.find('[data-name="comment-form"]').html();
      this.serviceXHR = null;

      this.cacheEls();
      this.bindEvents();

      this.initialize();
    },

    cacheEls: function() {
      this.$commentsCount = $('.comments-count');
      this.$commentsList = this.$top.find('.comments-list');
    },

    bindEvents: function() {
      var _this = this;
    },

    initialize: function() {
      this.initializeCommentForm();
      this.loadComments();
    },

    initializeForm: function($appendTo) {
      var $form = $(this.formTemplate);

      $form.appendTo($appendTo);

      return $form;
    },

    initializeCommentForm: function() {
      var _this = this;
      var $form = this.initializeForm(this.$top.find('.comment-form-container'));

      $form.find('[name="comment"]').focus(function() {
        _this.$top.find('.comment-form.reply').remove();
        $form.addClass('active');
      });
      $form.find('.cta.cancel').click(function() {
        $form.removeClass('active');
      });
    },

    initializeReplyForm: function(inReplyToId, $comment, e) {
      var _this = this;
      var $form = this.initializeForm($comment.find('> .reply-form-container'));

      e.preventDefault();

      //remove all existing reply forms
      this.$top.find('form.reply').remove();

      $form.addClass('reply');
      $form.find('[name="comment"]').focus(function() {
        _this.$top.find('.comment-form').removeClass('active');
        $form.addClass('active');
      });

      $form.find('.cta.cancel').click(function() {
        $form.remove();
      });

      $form.find('[name="comment"]').focus();
    },

    loadComments: function() {
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
          $reply.addClass('reply');
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
      $comment.attr('data-comment-id', data.id);

      $comment.find('.reply-btn').click($.proxy(this.initializeReplyForm, this, data.id, $comment));

      return $comment;
    }
  };
}(jQuery));
