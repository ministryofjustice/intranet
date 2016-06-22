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

      this.$submitCta.attr('data-original-label', this.$submitCta.val());
    },

    cacheEls: function() {
      this.$passwordField = this.$top.find('[name="password"]');
      this.$reenterPasswordField = this.$top.find('[name="reenter_password"]');
      this.$submitCta = this.$top.find('.cta[type="submit"]');
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
        this.toggleState('loading');

        //**/ window.setTimeout(function() {
          $.ajax({
            url: window.location.href,
            method: 'post',
            data: _this.getData(),
            success: $.proxy(_this.submitSuccess, _this),
            error: $.proxy(_this.submitError, _this),
            complete: $.proxy(_this.submitComplete, _this)
          });
        //**/}, 2000);
      }
      else {
        this.validation.displayErrors();
      }
    },

    getData: function() {
      var url = App.tools.url(true);
      return {
        key: url.param('key'),
        login: url.param('login'),
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

    submitComplete: function() {
      this.toggleState();
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
    },

    toggleState: function(state) {
      if(state === 'loading') {
        this.$submitCta.val('Loading...');
        this.$submitCta.addClass('loading');
        this.$submitCta.attr('disabled', 'disabled');
      }
      else {
        this.$submitCta.val(this.$submitCta.attr('data-original-label'));
        this.$submitCta.removeClass('loading');
        this.$submitCta.removeAttr('disabled');
      }
    }
  };

}(jQuery));
