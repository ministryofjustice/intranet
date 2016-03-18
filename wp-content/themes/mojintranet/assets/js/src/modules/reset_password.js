/** Reset password form
 */
(function($) {
  "use strict";

  var App = window.App;

  App.ResetPasswordForm = function() {
    this.$top = $('.reset-password-form');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.ResetPasswordForm.prototype = {
    init: function() {
      this.validation = new App.tools.Validation(this.$top);

      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$passwordField = this.$top.find('[name="password"]');
      this.$reenterPasswordField = this.$top.find('[name="reenter_password"]');
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
          data: this.getData(),
          success: $.proxy(this.submitSuccess, _this),
          error: $.proxy(this.submitError, _this)
        });
      }
      else {
        this.validation.displayErrors();
      }
    },

    getData: function() {
      return {
        key: App.tools.getUrlParam('key'),
        login: App.tools.getUrlParam('login'),
        password: this.$passwordField.val(),
        reenter_password: this.$reenterPasswordField.val()
      };
    },

    submitSuccess: function(data) {
      if(data.success) {
        this.displayConfirmationMessage();
      }
      else {
        if(data.validation.errors.length) {
          this.validation.displayErrors(data.validation.errors);
        }
        else {
          //the link's expired, so reloading the page will show the server-generated error
          window.location.href = window.location.href;
        }
      }
    },

    submitError: function() {
    },

    displayConfirmationMessage: function() {
      $('.template-container').addClass('confirmation');
    },

    validate: function() {
      var passwordVal1, passwordVal2;

      this.validation.reset();

      this.validation.isFilled(this.$passwordField, 'password');
      this.validation.isFilled(this.$reenterPasswordField, 're-enter password', 'Please re-enter password');

      passwordVal1 = this.$passwordField.val();
      passwordVal2 = this.$reenterPasswordField.val();

      if(passwordVal1 && passwordVal1.length < 8) {
        this.validation.error(this.$passwordField, 'password', 'Password must be at least 8 characters long');
      }

      if(passwordVal1 && passwordVal2 && passwordVal1 !== passwordVal2) {
        this.validation.error(this.$reenterPasswordField, 're-enter password', 'Passwords don\'t match');
      }
    }
  };

}(jQuery));
