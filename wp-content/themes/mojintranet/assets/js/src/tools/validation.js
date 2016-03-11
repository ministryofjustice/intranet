(function($) {
  "use strict";

  var App = window.App;

  App.tools.Validation = function($form) {
    this.$form = $form;
    this.errors = [];

    this.messageTemplate = $('.template-partial[data-name="validation-message"]').html();
    this.summaryTemplate = $('.template-partial[data-name="validation-summary"]').html();
    this.summaryItemTemplate = $('.template-partial[data-name="validation-summary-item"]').html();
  };

  App.tools.Validation.prototype = {
    error: function($element, fieldName, message) {
      $element = this.getElement($element);

      this.errors.push({
        element: $element,
        fieldName: fieldName,
        message: message
      });
    },

    isFilled: function($element, fieldName, message) {
      var value;

      $element = this.getElement($element);

      value = $element.val();

      if(!message) {
        message = 'Please enter ' + fieldName;
      }

      if(value === '') {
        this.error($element, fieldName, message);

        return false;
      }

      return true;
    },

    isValidEmail: function($element, fieldName, message) {
      var value;

      $element = this.getElement($element);

      value = $element.val();

      if(!message) {
        message = App.tools.ucfirst(fieldName) + ' must be a valid email address';
      }

      if(!/[^ ]+@[^ ]+/.test(value)) {
        this.error($element, fieldName, message);

        return false;
      }

      return true;
    },

    hasErrors: function($element) {
      var a, count;

      $element = this.getElement($element);

      if($element) {
        for(a = 0, count = this.errors.length; a < count; a++) {
          if(this.errors[a].element.is($element)) {
            return true;
          }
        }

        return false;
      }

      return this.errors.length > 0;
    },

    getErrors: function() {
      return this.errors;
    },

    displayErrors: function(data) {
      var $element, $message, $container;
      var error;
      var index, count;
      var errors = this.normalizeData(data) || this.errors;

      this.displaySummary(errors);

      for(index = 0, count = errors.length; index < count; index++) {
        error = errors[index];
        $element = error.element;
        $message = this.createMessage(error.message);
        $element.closest('.form-row').addClass('validation-error');
        $element.before($message);
      }
    },

    normalizeData: function(data) {
      var _this = this;
      var newData = [];

      if(!data) {
        return null;
      }

      $.each(data.data, function(index, error) {
        newData.push({
          element: _this.$form.find('[name="' + error.name + '"]'),
          fieldName: error.field_name,
          message: error.message
        });
      });

      return newData;
    },

    reset: function() {
      this.$form.find('.validation-summary').remove();
      this.$form.find('.validation-message').remove();
      this.$form.find('.form-row.validation-error').removeClass('validation-error');

      this.errors = [];
    },

    createMessage: function(message) {
      var $message = $(this.messageTemplate);

      $message.html(message);

      return $message;
    },

    displaySummary: function(errors) {
      var $summary = $(this.summaryTemplate);
      var $summaryItem;
      var $list = $summary.find('.errors');
      var index, count;

      for(index = 0, count = errors.length; index < count; index++) {
        $summaryItem = $(this.summaryItemTemplate);
        $summaryItem.html(App.tools.ucfirst(errors[index].fieldName));
        $list.append($summaryItem);
      }

      $summary.prependTo(this.$form);
    },

    getElement: function($element) {
      if($.type($element) === 'string') {
        return $($element);
      }

      return $element;
    }
  };
}(jQuery));
