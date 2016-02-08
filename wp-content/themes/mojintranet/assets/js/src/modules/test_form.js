(function($) {
  "use strict";

  var TestForm = function() {
    this.$form = $('.standard.test');
    this.validation = new App.tools.Validation(this.$form);
    this.bindEvents();
  };

  TestForm.prototype = {
    bindEvents: function() {
      var _this = this;

      this.$form.submit($.proxy(_this.submitForm, _this));
    },

    submitForm: function(e) {
      var email1, email2;

      e.preventDefault();

      this.validation.isFilled('[name="first-name"]', 'first name');
      this.validation.isFilled('[name="surname"]', 'surname');
      email1 = this.validation.isFilled('[name="login-email"]', 'email address');
      email2 = this.validation.isFilled('[name="reenter-login-email"]', 're-enter email address', 'Please re-enter your email address');

      if(email1 && email2 && email1 !== email2) {
        console.log('yeah, in', email1, email2);
        this.validation.error('[name="reenter-login-email"]', 're-enter email address', 'Email addresses don\'t match');
      }

      this.validation.displayErrors();
    }
  };

  window.testForm = new TestForm();
}(jQuery));
