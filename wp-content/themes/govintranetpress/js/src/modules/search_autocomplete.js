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

      if(key === 40) { //down
        if(!$highlighted.length) {
          $highlighted = this.$autocompleteBox.find('.item').first();
          $highlighted.addClass('highlighted');
        }
        else {
          //highlight next
          $highlighted.removeClass('highlighted');
          $highlighted.next().addClass('highlighted');
        }
      }
      else if(key === 38) { //up
        if(!$highlighted.length) {
          $highlighted = this.$autocompleteBox.find('.item').last();
          $highlighted.addClass('highlighted');
        }
        else {
          //highlight next
          $highlighted.removeClass('highlighted');
          $highlighted.prev().addClass('highlighted');
        }
      }
      else if(key === 13) { //enter
        if($highlighted.length) {
          e.preventDefault();
          window.location.href = $highlighted.attr('data-url');
        }
      }
      else if(key === 27) { //esc
        this.emptyAutocompleteBox();
      }
    },

    autocompleteTypingHandle: function(e) {
      var key = e.which || e.keyCode;
      var $highlighted;

      this.appendAutocompleteBox($(e.target));

      if(this.lastKeywordsLength !== this.$searchField.val().length) {
        this.lastKeywordsLength = this.$searchField.val().length;
        this.requestResults($(e.target).val());
      }
    },

    createAutocompleteBox: function() {
      this.$autocompleteBox = $('<ul></ul>')
        .addClass('autocomplete-list');
    },

    emptyAutocompleteBox: function() {
      this.$autocompleteBox.empty();
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

      this.emptyAutocompleteBox();

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
          console.log(data.url);
          window.location.href = data.url;
        });

      return $row;
    }
  };
}(jQuery));
