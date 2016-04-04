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
      this.postId = $('.template-container').attr('data-post-id');
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl + '/service/comments/' + this.postId;

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
      this.$loadMoreContainer = this.$top.find('.load-more-container');
      this.$loadMoreBtn = this.$top.find('.load-more');
    },

    bindEvents: function() {
      var _this = this;

      this.$loadMoreBtn.click(function(e) {
        _this.$loadMoreContainer.addClass('loading');

        /* use the timeout for dev/debugging purposes */
        //**/window.setTimeout(function() {
          $.getJSON(_this.serviceUrl, $.proxy(_this.displayComments, _this));
        //**/}, 2000);
      });
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
        _this.$top.find('.comment-form.reply-form').remove();
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
      this.$top.find('form.reply-form').remove();

      $form.addClass('reply-form');
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
          $reply = this.buildComment(reply, true);
          $reply.addClass('last-two');
          $comment.find('> .replies-list').append($reply);
        }
      }

      this.$commentsCount.find('.count').html(data.total_comments);
      this.$loadMoreContainer.removeClass('loading');

      App.ins.like.initializeLikes();
    },

    displayReplies: function($comment, data) {
      var a;
      var totalReplies;
      var reply;
      var $reply;

      this.setCommentState('loaded', $comment);
      //remove existing replies
      $comment.find('> .replies-list > .reply').remove();

      for(a = 0, totalReplies = data.comments.length; a < totalReplies; a++) {
        reply = data.comments[a];
        $reply = this.buildComment(reply, true);
        $comment.find('> .replies-list').append($reply);
      }

      $comment.find('> .replies-list > .reply').slice(-2).addClass('last-two');
    },

    buildComment: function(data, isReply) {
      var $comment = $(this.itemTemplate);

      $comment.find('.content').html(data.comment);
      $comment.find('.datetime').html(data.date_posted);
      $comment.find('.author').html(data.author);
      $comment.find('.likes .count').html(data.likes);
      $comment.find('.like-container').attr('data-likes-count', data.likes);
      $comment.find('.like-container').attr('data-post-id', data.id);
      $comment.attr('data-comment-id', data.id);

      $comment.find('.reply-btn').click($.proxy(this.initializeReplyForm, this, data.id, $comment));

      if(isReply) {
        $comment.addClass('reply');
      }
      else {
        $comment.find('.toggle-replies').click($.proxy(this.toggleReplies, this, $comment));
      }

      return $comment;
    },

    toggleReplies: function($comment, e) {
      var _this = this;
      var id = $comment.attr('data-comment-id');

      e.preventDefault();

      if($comment.hasClass('opened')) { //toggle close
        this.setCommentState('closed', $comment);
      }
      else { //toggle open
        if($comment.hasClass('loaded')) { //already fetched
          this.setCommentState('opened', $comment);
        }
        else { //not fetched yet
          this.setCommentState('loading', $comment);

          /* use the timeout for dev/debugging purposes */
          //**/window.setTimeout(function() {
            $.getJSON(_this.serviceUrl + '/' + id, $.proxy(_this.displayReplies, _this, $comment));
          //**/}, 500);
        }
      }
    },

    setCommentState: function(state, $comment) {
      var $toggleReplies = $comment.find('> .toggle-replies');

      if(state === 'opened') {
        $comment.addClass('opened');
        $toggleReplies.html('Hide replies');
      }
      else if(state === 'closed') {
        $comment.removeClass('opened');
        $toggleReplies.html('View all replies');
      }
      else if(state === 'loaded') {
        $comment.removeClass('loading');
        $comment.addClass('loaded opened');
        $toggleReplies.html('Hide replies');
      }
      else if(state === 'loading') {
        $comment.addClass('loading');
      }
    }
  };
}(jQuery));
