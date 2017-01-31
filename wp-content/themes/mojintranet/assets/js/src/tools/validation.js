(function($) {
  "use strict";

  var App = window.App;

  App.tools.Validation = function($form, $summaryContainer) {
    this.$form = $form;
    this.$summaryContainer = $summaryContainer || $form;
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
      value = App.tools.trim(value);
      fieldName = fieldName.charAt(0).toUpperCase() + fieldName.slice(1);

      if(!message) {
        message = fieldName + ' cannot be empty';
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

    normalizeData: function(errors) {
      var _this = this;
      var newData = [];

      if(!errors) {
        return null;
      }

      $.each(errors, function(index, error) {
        newData.push({
          element: _this.$form.find('[name="' + error.name + '"]'),
          fieldName: error.field_name,
          message: error.message
        });
      });

      return newData;
    },

    setErrorMessage: function(errors, errorCode, value) {
      $.each(errors, function(index, error) {
        if(errors[index].error_code === errorCode) {
          errors[index].message = value;
        }
      });
    },

    reset: function() {
      this.$summaryContainer.find('.validation-summary').remove();
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

      $summary.prependTo(this.$summaryContainer);
    },

    getElement: function($element) {
      if($.type($element) === 'string') {
        return $($element);
      }

      return $element;
    }
  };
}(jQuery));
