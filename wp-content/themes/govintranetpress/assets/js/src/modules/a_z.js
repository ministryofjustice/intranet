/** A-Z page
 */
(function($) {
  "use strict";

  var App = window.App;

  App.AZIndex = function() {
    this.$top = $('.a-z');
    if(!this.$top.length) { return; }
    this.init();

    this.goToLetter(null);
  };

  App.AZIndex.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/search';
      this.pageBase = this.applicationUrl+'/'+this.$top.data('top-level-slug');

      this.itemTemplate = this.$top.find('template[data-name="a-z-result-item"]').html();
      this.serviceXHR = null;

      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$categoryInput = this.$top.find('[name="category"]');
      this.$keywordsInput = this.$top.find('[name="keywords"]');
      this.$letters = this.$top.find('.letter');
      this.$results = this.$top.find('.results');
    },

    bindEvents: function() {
      var _this = this;
      this.$letters.click(function(e) {
        e.preventDefault();
        var $letter = $(this);
        var letter = $letter.data('letter');
        _this.$letters.removeClass('selected');
        $letter.addClass('selected');
        _this.loadResults();
      });

      this.$keywordsInput.keyup(function(e) {
        _this.loadResults();
      });
    },

    loadResults: function(requestData) {
      var _this = this;

      requestData = this.getRequestData(requestData);

      this.stopLoadingResults();
      this.requestResults(requestData);
    },

    stopLoadingResults: function() {
      //this.$tree.find('.item.loading').removeClass('loading');
      if(this.serviceXHR) {
        this.serviceXHR.abort();
        this.serviceXHR = null;
      }
    },

    requestResults: function(data) {
      var dataArray = [];

      $.each(data, function(key, value) {
        dataArray.push(value);
      });

      this.serviceXHR = $.getJSON(this.serviceUrl+'/'+dataArray.join('/'), $.proxy(this.displayResults, this));
    },

    clearResults: function() {
      this.$results.empty();
    },

    displayResults: function(data) {
      var _this = this;
      var $child;

      this.clearResults();

      $.each(data.data, function(index, group) {
        $.each(group.results, function(groupIndex, result) {
          $child = _this.buildResultRow(result);
          _this.$results.append($child);
        });
      });
    },

    getSelectedInitial: function() {
      return this.$letters.filter('.selected').data('letter');
    },

    buildResultRow: function(data) {
      var _this = this;
      var $child = $(this.itemTemplate);
      $child.find('.title').html(data.title);
      $child.find('.description').html(data.excerpt);

      return $child;
    },

    goToLetter: function(letter) {
      if(!letter) {
        letter = 'All';
      }
      else{
        letter = letter.toUpperCase();
      }

      this.$letters.removeClass('selected');
      this.$letters.filter('[data-letter="'+letter+'"]').addClass('selected');
      this.loadResults();
    },

    getRequestData: function(data) {
      var _this = this;

      var base = {
        'type': '',
        'category': '',
        'keywords': _this.$keywordsInput.val(),
        'initial': _this.getSelectedInitial(),
        'page': 1,
        'resultsPerPage': 20
      };

      if(data) {
        $.each(data, function(key, value) {
          base[key] = value;
        });
      }

      return base;
    }
  };
}(jQuery));
