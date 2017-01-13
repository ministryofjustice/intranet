/** Register form
 */
(function($) {
  "use strict";

  var App = window.App;

  App.RegisterForm = function() {
    this.$top = $('.register-form');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.RegisterForm.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl + '/user/request/';
      this.validation = new App.tools.Validation(this.$top);

      this.cacheEls();
      this.bindEvents();

      this.$submitCta.attr('data-original-label', this.$submitCta.val());
    },

    cacheEls: function() {
      this.$displayNameField = this.$top.find('[name="display_name"]');
      this.$emailField = this.$top.find('[name="email"]');
      this.$submitCta = this.$top.find('.cta[type="submit"]');
      this.$confirmationScreen = $('.confirmation-screen');
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
            url: _this.serviceUrl,
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
      return {
        email: this.$emailField.val(),
        display_name: this.$displayNameField.val(),
        redirect_url: window.location.href
      };
    },

    submitSuccess: function(data) {
      if(data.success) {
        this.toggleConfirmationMessage(true);
      }
      else {
        this.validation.displayErrors(data.validation.errors);
      }
    },

    submitError: function() {
    },

    submitComplete: function() {
      this.toggleState();
    },

    toggleConfirmationMessage: function(toggle) {
      $('.template-container').toggleClass('confirmation', toggle);
    },

    validate: function() {
      var emailVal1, emailVal2;

      this.validation.reset();

      this.validation.isFilled(this.$emailField, 'email');
      this.validation.isFilled(this.$displayNameField, 'screen name');
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
