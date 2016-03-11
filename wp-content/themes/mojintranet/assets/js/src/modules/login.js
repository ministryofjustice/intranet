/** Register form
 */
(function($) {
  "use strict";

  var App = window.App;

  App.LoginForm = function() {
    this.$top = $('.login-form');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.LoginForm.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.validation = new App.tools.Validation(this.$top);

      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$emailField = this.$top.find('[name="email"]');
      this.$passwordField = this.$top.find('[name="password"]');
    },

    bindEvents: function() {
      var _this = this;

      this.$top.on('submit', $.proxy(this.submit, this));
    },

    submit: function(e) {
      var _this = this;

      e.preventDefault();

      this.validate();

      if(!this.validation.hasErrors()) {
        $.ajax({
          url: window.location.href,
          method: 'post',
          //dataType: 'json',
          data: this.getData(),
          success: $.proxy(this.submitSuccess, _this),
          error: $.proxy(this.submitError, _this)
        });
        this.displayConfirmationMessage();
      }
      else {
        this.validation.displayErrors();
      }
    },

    getData: function() {
      return {
        email: this.$emailField.val(),
        password: this.$passwordField.val()
      };
    },

    submitSuccess: function(data) {
      if(data.success) {
        window.location.href = this.applicationUrl;
      }
      else {
        this.validation.displayErrors(data);
      }
    },

    submitError: function() {
    },

    displayConfirmationMessage: function() {
      $('.template-container').addClass('confirmation');
    },

    validate: function() {
      this.validation.reset();

      this.validation.isFilled(this.$emailField, 'email');
      this.validation.isFilled(this.$passwordField, 'password');
    }
  };

}(jQuery));
