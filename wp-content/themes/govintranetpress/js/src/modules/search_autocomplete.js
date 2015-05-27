/** SearchAutocomplete
 */
(function($) {
  "use strict";

  var App = window.App;

  App.SearchAutocomplete = function() {
    this.$top = $('.search-form');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.SearchAutocomplete.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/search';

      this.cacheEls();
      this.createAutocompleteBox();
      this.bindEvents();
      this.turnNativeAutocompleteOff();

      this.$searchField.each(function() {
        $(this).attr('data-current-keywords', $(this).val());
      });

      this.lastKeywordsLength = this.$searchField.val().length;
    },

    cacheEls: function() {
      this.$searchField = this.$top.find('.keywords-field');
    },

    bindEvents: function() {
      var _this = this;
      this.$searchField.on('keyup', $.proxy(this.autocompleteTypingHandle, this));
      this.$searchField.on('keypress', $.proxy(this.autocompleteNavigationHandle, this));
      //this.$searchField.on('blur', $.proxy(this.emptyAutocompleteBox, this));
    },

    turnNativeAutocompleteOff: function() {
      this.$top.attr('autocomplete', 'off');
    },

    autocompleteNavigationHandle: function(e) {
      var key = e.which || e.keyCode;
      var $highlighted = this.$autocompleteBox.find('.highlighted');
      var $target = $(e.target);
      var val;

      if(key === 40) { //down
        e.preventDefault();

        if(!$highlighted.length) {
          $highlighted = this.$autocompleteBox.find('.item').first();
          $highlighted.addClass('highlighted');
        }
        else {
          //highlight next
          $highlighted.removeClass('highlighted');
          $highlighted = $highlighted.next();
          $highlighted.addClass('highlighted');
        }

        val = $highlighted.text();

        if(val.length) {
          $target.val(val);
        }
        else {
          $target.val($target.attr('data-current-keywords'));
        }
      }
      else if(key === 38) { //up
        e.preventDefault();

        if(!$highlighted.length) {
          $highlighted = this.$autocompleteBox.find('.item').last();
          $highlighted.addClass('highlighted');
        }
        else {
          //highlight previous
          $highlighted.removeClass('highlighted');
          $highlighted = $highlighted.prev();
          $highlighted.addClass('highlighted');
        }

        val = $highlighted.text();

        if(val.length) {
          $target.val(val);
        }
        else {
          $target.val($target.attr('data-current-keywords'));
        }
      }
      else if(key === 13) { //enter
        e.preventDefault();

        if($highlighted.length) {
          window.location.href = $highlighted.attr('data-url');
        }
      }
      else if(key === 27) { //esc
        this.hideAutocompleteBox();
      }
    },

    autocompleteTypingHandle: function(e) {
      var key = e.which || e.keyCode;
      var $highlighted;
      var $target = $(e.target);

      if(key === 38 || key === 40) {
        return;
      }

      this.appendAutocompleteBox($target);

      $target.attr('data-current-keywords', $target.val());

      if(this.lastKeywordsLength !== this.$searchField.val().length) {
        this.lastKeywordsLength = this.$searchField.val().length;
        this.requestResults($target.val());
      }
    },

    createAutocompleteBox: function() {
      this.$autocompleteBox = $('<ul></ul>')
        .addClass('autocomplete-list');
    },

    emptyAutocompleteBox: function() {
      this.$autocompleteBox.empty();
    },

    hideAutocompleteBox: function() {
      this.$autocompleteBox.addClass('hidden');
    },

    showAutocompleteBox: function() {
      this.$autocompleteBox.removeClass('hidden');
      this.emptyAutocompleteBox();
    },

    appendAutocompleteBox: function($target) {
      $target.after(this.$autocompleteBox);
    },

    requestResults: function(keywords) {
      var _this = this;
      var data = {};
      var dataArray = [];

      keywords = this.sanitizeKeywords(keywords);

      if(!keywords.length) {
        return;
      }

      data = {
        'type': '',
        'category': '',
        'keywords': App.tools.urlencode(keywords),
        'page': 1,
        'resultsPerPage': 10
      };

      $.each(data, function(key, value) {
        dataArray.push(value);
      });

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl+'/'+dataArray.join('/'), $.proxy(_this.displayResults, _this));
      //**/}, 2000);
    },

    sanitizeKeywords: function(keywords) {
      keywords = keywords.replace(/\s+/g, ' ');
      keywords = keywords.replace(/^\s+|\s+$/g, '');

      return keywords;
    },

    displayResults: function(data) {
      var _this = this;
      var $row;

      this.showAutocompleteBox();

      $.each(data.results, function(index, result) {
        $row = _this.buildResultRow(result);
        $row.appendTo(_this.$autocompleteBox);
      });
    },

    buildResultRow: function(data) {
      var $row = $('<li></li>');
      $row
        .addClass('item')
        .html(data.title)
        .attr('data-url', data.url)
        .click(function() {
          window.location.href = data.url;
        });

      return $row;
    }
  };
}(jQuery));
