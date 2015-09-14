/** SearchAutocomplete
 */
(function($) {
  "use strict";

  var App = window.App;

  App.SearchAutocomplete = function() {
    this.$top = $('.search-form:not(.no-dw-autocomplete)');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.SearchAutocomplete.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/search';
      this.serviceXHR = null;

      this.cacheEls();
      this.createList();
      this.bindEvents();
      this.setUpTheForms();

      this.lastKeywordsLength = this.$searchField.val().length;
    },

    cacheEls: function() {
      this.$searchField = this.$top.find('.keywords-field');
    },

    bindEvents: function() {
      var _this = this;
      this.$searchField.on('keyup', $.proxy(this.autocompleteTypingHandle, this));
      this.$searchField.on('keydown', $.proxy(this.autocompleteNavigationHandle, this));
      $(document).on('click', $.proxy(this.outsideClickHandle, this));
    },

    setUpTheForms: function() {
      this.$top.attr('autocomplete', 'off');

      this.$searchField.each(function() {
        $(this).attr('data-current-keywords', $(this).val());
      });
    },

    autocompleteNavigationHandle: function(e) {
      var key = e.which || e.keyCode;
      var $highlighted = this.$list.find('.highlighted');
      var $target = $(e.target);
      var val;

      if(key === 40) { //down
        if(!$highlighted.length) {
          $highlighted = this.$list.find('.item').first();
        }
        else {
          //highlight next
          $highlighted.removeClass('highlighted');
          $highlighted = $highlighted.next();
        }
      }
      else if(key === 38) { //up
        if(!$highlighted.length) {
          $highlighted = this.$list.find('.item').last();
        }
        else {
          //highlight previous
          $highlighted.removeClass('highlighted');
          $highlighted = $highlighted.prev();
        }
      }
      else if(key === 13) { //enter
        if($highlighted.length) {
          e.preventDefault();

          window.location.href = $highlighted.attr('data-url');
        }
        else {
          $target.closest('.search-form').submit();
        }
      }
      else if(key === 27) { //esc
        this.hideList();
        $target.val($target.attr('data-current-keywords'));
      }

      if(key === 38 || key === 40) {
        e.preventDefault();

        $highlighted.addClass('highlighted');

        val = $highlighted.text();

        if(val.length) {
          $target.val(val);
        }
        else {
          $target.val($target.attr('data-current-keywords'));
        }

        if(this.isListEmpty()) {
          this.requestResults($target, true);
        }
      }
    },

    autocompleteTypingHandle: function(e) {
      var _this = this;
      var key = e.which || e.keyCode;
      var $highlighted;
      var $target = $(e.target);
      var typingTimeout;

      if(key === 38 || key === 40 || key === 27) {
        return;
      }

      $target.attr('data-current-keywords', $target.val());

      clearTimeout(typingTimeout);
      typingTimeout = setTimeout(function() {
        _this.requestResults($target);
      },500);
    },

    outsideClickHandle: function(e) {
      var $target = $(e.target);

      if(!$target.is(this.$list) && !$target.next().is(this.$list)) {
        this.hideList();
      }
    },

    createList: function() {
      this.$list = $('<ul></ul>')
        .addClass('autocomplete-list');
    },

    emptyList: function() {
      this.$list.empty();
    },

    hideList: function() {
      this.$list.addClass('hidden');
      this.emptyList();
    },

    showList: function() {
      this.$list.removeClass('hidden');
      this.emptyList();
    },

    appendList: function($target) {
      $target.after(this.$list);
    },

    isListEmpty: function() {
      return !this.$list.find('.item').length;
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
    },

    requestResults: function($target, forceResults) {
      var _this = this;
      var data = {};
      var dataArray = [];
      var keywords = this.sanitizeKeywords($target.val());


      if(!keywords.length) {
        return;
      }

      if(this.lastKeywordsLength !== keywords.length) {
        this.lastKeywordsLength = keywords.length;
      }
      else if(!forceResults) {
        return;
      }

      this.stopLoadingResults();
      this.appendList($target);
      this.hideList();

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

      this.$list.addClass('loading');

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl+'/'+dataArray.join('/'), $.proxy(_this.displayResults, _this));
      //**/}, 2000);
    },

    stopLoadingResults: function() {
      if(this.serviceXHR) {
        this.serviceXHR.abort();
      }
      this.$list.removeClass('loading');
    },

    displayResults: function(data) {
      var _this = this;
      var $row;

      if(data.results.length) {
        this.showList();
      }

      $.each(data.results, function(index, result) {
        $row = _this.buildResultRow(result);
        $row.appendTo(_this.$list);
      });

      this.serviceXHR = null;
    },

    sanitizeKeywords: function(keywords) {
      keywords = keywords.replace(/\s+/g, ' ');
      keywords = keywords.replace(/^\s+|\s+$/g, '');

      return keywords;
    }
  };
}(jQuery));
