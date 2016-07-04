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
      this.settings = {
        characterLimit: 2000,
        commentsPerPage: 10
      };

      this.postId = $('.template-container').attr('data-post-id');
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl + '/service/comments/' + this.postId;

      /*Note: Since we have multiple comment forms on this page, $form will be
        set dynamically when validating a particular form, so using just a
        placeholder here */
      this.validation = new App.tools.Validation($([]), $('.validation-summary-container'));

      this.itemTemplate = this.$top.find('[data-name="comment-item"]').html();
      this.formTemplate = this.$top.find('[data-name="comment-form"]').html();
      this.badWordsErrorTemplate = this.$top.find('[data-name="bad-words-error"]').html();
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
      this.$loadMoreBtn.click($.proxy(this.loadMore, this));
    },

    loadMore: function() {
      var _this = this;
      var lastId = this.getLastId() || 0;
      var url = this.serviceUrl + '/' + [0, lastId, this.settings.commentsPerPage].join('/');
      _this.$loadMoreContainer.addClass('loading');

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        $.getJSON(url, $.proxy(_this.displayComments, _this));
      //**/}, 2000);
    },

    initialize: function() {
      //set the url for the sign in link
      var currentUrl = App.tools.url(true);
      var signInUrl = App.tools.url(false);

      signInUrl
        .authority(currentUrl.authority())
        .segment('sign-in')
        .param('return_url', App.tools.urlencode(currentUrl.get()));

      this.$top.find('.sign-in-link').attr('href', signInUrl.get());

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
      var $textarea = $form.find('[name="comment"]');
      var commentTooLong = false;

      $textarea
        .focus(function() {
          _this.validation.reset();
          _this.$top.find('.comment-form.reply-form').remove();
          $form.addClass('active');
        })
        .keyup(function() {
          commentTooLong = $textarea.val().length > _this.settings.characterLimit;
          $form.toggleClass('character-limit-reached', commentTooLong);
          $form.find('.cta.submit').prop('disabled', commentTooLong);
        });

      $form.find('.cta.cancel').click(function() {
        _this.validation.reset();
        $form.removeClass('active character-limit-reached');
      });

      $form.submit($.proxy(this.submitForm, this));
    },

    initializeReplyForm: function(inReplyToId, $comment, e) {
      var _this = this;
      var $form = this.initializeForm($comment.find('> .reply-form-container'));

      e.preventDefault();

      //remove all existing reply forms
      this.$top.find('form.reply-form').remove();

      $form.addClass('reply-form');
      $form.find('[name="comment"]').focus(function() {
        _this.validation.reset();
        _this.$top.find('.comment-form').removeClass('active');
        $form.addClass('active');
      });

      $form.find('.cta.cancel').click(function() {
        _this.validation.reset();
        $form.remove();
      });

      $form.find('[name="comment"]').focus();

      $form.submit($.proxy(this.submitForm, this));
    },

    submitForm: function(e) {
      var _this = this;
      var $form = $(e.target);
      var inReplyToId = $form.closest('.comment').attr('data-comment-id');
      var $parentComment = $form.closest('.comment:not(.reply)');
      var rootCommentId = $parentComment.attr('data-comment-id');

      e.preventDefault();

      //we have multiple forms so need to swap them at this point
      this.validation.$form = $form;

      this.validate($form);

      if(!this.validation.hasErrors()) {
        $.ajax({
          url: this.serviceUrl,
          method: 'put',
          data: {
            comment: $form.find('[name="comment"]').val(),
            in_reply_to_id: inReplyToId,
            root_comment_id: rootCommentId
          },
          success: function(data) {
            if(data.success) {
              window.location.href = window.location.href;
            }
            else {
              _this.validation.setErrorMessage(data.validation.errors, 'bad_words', _this.badWordsErrorTemplate);
              _this.validation.displayErrors(data.validation.errors);
            }
          }
        });
      }
      else {
        this.validation.displayErrors();
      }
    },

    loadComments: function() {
      var _this = this;
      var url = this.serviceUrl + '/' + [0, 0, this.settings.commentsPerPage].join('/');

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(url, $.proxy(_this.displayComments, _this));
      //**/}, 2000);
    },

    displayComments: function(data) {
      var a, b;
      var totalComments, totalReplies;
      var comment, reply;
      var $comment, $reply;
      var loadedCommentsCount = 0;

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

        if(comment.total_replies > 2) {
          $comment.addClass('has-more-replies');
          $comment.find('.toggle-replies .count').html(comment.total_replies);
        }
      }

      this.$commentsCount.find('.count').html(data.total_comments);
      this.$loadMoreContainer.removeClass('loading');

      loadedCommentsCount = this.$commentsList.find('.comment:not(.reply)').length;

      if(loadedCommentsCount >= data.total_comments || data.retrieved_comments === 0) {
        this.$loadMoreContainer.addClass('hidden');
      }

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

      App.ins.like.initializeLikes();
    },

    buildComment: function(data, isReply) {
      var $comment = $(this.itemTemplate);
      var signInUrl = this.applicationUrl + '/sign-in/?return_url=' + App.tools.urlencode(window.location.href);
      var comment = data.comment;
      var relativeTime = window.moment(data.date_posted).fromNow();

      //convert urls to links
      comment = comment.replace(/https?:\/\/[^\s]+/g, function(match) {
        return '<a href="' + match + '">' + match + '</a>';
      });

      //add line breaks
      comment = comment.replace(/\n+/g, '<br>');

      $comment.find('.content').html(comment);
      $comment.find('.datetime').html(relativeTime);
      $comment.find('.author').html(data.author_name);
      $comment.find('.likes .count').html(data.likes);
      $comment.find('.like-container').attr('data-likes-count', data.likes);
      $comment.find('.like-container').attr('data-post-id', data.id);
      $comment.attr('data-comment-id', data.id);

      if(App.tools.isUserLoggedIn()) {
        $comment.find('.reply-btn').click($.proxy(this.initializeReplyForm, this, data.id, $comment));
      }
      else {
        $comment.find('.reply-btn').attr('href', signInUrl);
      }

      if(isReply) {
        $comment.addClass('reply');
      }
      else {
        $comment.find('.toggle-replies').click($.proxy(this.toggleReplies, this, $comment));
      }

      return $comment;
    },

    validate: function($form) {
      this.validation.reset();

      this.validation.isFilled($form.find('[name="comment"]'), 'comment');
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
    },

    getLastId: function() {
      var $lastComment = this.$top.find('.comment:not(.reply)').last();
      return $lastComment.attr('data-comment-id');
    }
  };
}(jQuery));
