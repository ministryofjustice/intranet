/** SearchResults
 */
(function($) {
  "use strict";

  var App = window.App;

  App.SearchResults = function() {
    this.$top = $('.page-search-results');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.SearchResults.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/search';
      this.pageBase = this.applicationUrl+'/'+this.$top.data('top-level-slug');

      this.itemTemplate = this.$top.find('.template-partial[data-name="search-item"]').html();
      this.resultsPageTitleTemplate = this.$top.find('.template-partial[data-name="search-results-page-title"]').html();
      this.filteredResultsTitleTemplate = this.$top.find('.template-partial[data-name="search-filtered-results-title"]').html();
      this.serviceXHR = null;
      this.months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      this.currentPage = null;

      this.cacheEls();
      this.bindEvents();

      this.$keywordsInput.focus();

      this.setFilters();

      this.loadResults();
    },

    cacheEls: function() {
      this.$searchForm = this.$top.find('#search-form');
      this.$typeInput = this.$top.find('[name="type"]');
      this.$categoryInput = this.$top.find('[name="category"]');
      this.$keywordsInput = this.$top.find('[name="keywords"]');
      this.$results = this.$top.find('.results');
      this.$prevPage = this.$top.find('.previous');
      this.$nextPage = this.$top.find('.next');
    },

    bindEvents: function() {
      var _this = this;

      //!!! TODO: this will require a fallback for IE's
      this.$keywordsInput.on('input', function(e) {
        _this.loadResults({
          page: 1
        });
      });

      this.$prevPage.click(function(e) {
        e.preventDefault();
        _this.loadResults({
          'page': $(this).attr('data-page')
        });
      });

      this.$nextPage.click(function(e) {
        e.preventDefault();
        _this.loadResults({
          'page': $(this).attr('data-page')
        });
      });

      this.$searchForm.on('submit', function(e) {
        e.preventDefault();
      });
    },

    setFilters: function() {
      var segments = this.getSegmentsFromUrl();
      var keywords;

      //set type field based on url segment
      if(segments[0]) {
        this.$typeInput.val(segments[0]);
      }

      if(segments[1]) {
        keywords = segments[1];
        keywords = decodeURI(keywords);
        keywords = keywords.replace('+', ' ');
        keywords = keywords.replace(/[^a-zA-Z0-9\s']+/g, '');

        //update keywords field with keywords from url
        if(keywords) {
          this.$keywordsInput.val(keywords === '-' ? '' : keywords);
        }
      }
    },

    loadResults: function(requestData) {
      var _this = this;

      requestData = this.getDataObject(requestData);

      this.stopLoadingResults();
      this.$top.addClass('loading-results');

      this.$top.find('.search-results-title').remove();
      this.$results.prepend($(this.resultsPageTitleTemplate).text('Loading results...'));

      this.$results.find('.search-item').addClass('faded');

      this.requestResults(requestData);
    },

    stopLoadingResults: function() {
      this.$top.removeClass('loading-results');
      this.$top.find('.search-group-separator.loading');
      if(this.serviceXHR) {
        this.serviceXHR.abort();
        this.serviceXHR = null;
      }
    },

    requestResults: function(data) {
      var _this = this;
      var dataArray = [];

      $.each(data, function(key, value) {
        dataArray.push(value);
      });

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl+'/'+dataArray.join('/'), $.proxy(_this.displayResults, _this));
      //**/}, 2000);
    },

    clearResults: function() {
      this.$results.empty();
    },

    displayResults: function(data) {
      var _this = this;
      var $searchItem;

      this.clearResults();
      this.setResultsHeading(data);

      $.each(data.results, function(index, result) {
        $searchItem = _this.buildResultRow(result);
        _this.$results.append($searchItem);
      });

      this.updatePagination(data);
      this.updateUrl();
      this.stopLoadingResults();
    },

    setResultsHeading: function(data) {
      var $resultsTitle = $(this.resultsPageTitleTemplate);
      var $filteredResultsTitle = $(this.filteredResultsTitleTemplate);
      var totalResults = parseInt(data.totalResults, 10);
      var resultsPage = parseInt(data.urlParams.page, 10);
      var date;
      var formattedDate;

      this.$results.append($filteredResultsTitle);
      $filteredResultsTitle.find('.results-count').text(totalResults);
      $filteredResultsTitle.find('.results-count-description').text(totalResults === 1 ? 'result' : 'results');

      if(this.hasKeywords()) {
        $filteredResultsTitle.find('.keywords').text(this.getSanitizedKeywords());
      }
      else {
        $filteredResultsTitle.find('.containing').hide();
        $filteredResultsTitle.find('.keywords').hide();
      }
    },

    hasKeywords: function() {
      return this.getSanitizedKeywords().length > 0;
    },

    getSanitizedKeywords: function() {
      var keywords = this.$keywordsInput.val();
      keywords = keywords.replace(/^\s+|\s+$/g, '');
      keywords = keywords.replace(/[^a-zA-Z0-9\s']+/g, ' ');
      keywords = keywords.replace(/\s+/g, ' ');
      return keywords;
    },

    buildResultRow: function(data) {
      var _this = this;
      var $child = $(this.itemTemplate);
      var date = this.parseDate(data.timestamp);

      if(data.thumbnail_url) {
        $child.find('.thumbnail').attr('src', data.thumbnail_url);
      }
      else {
        $child.find('.thumbnail').remove(); //we don't want an img element with no src
      }

      $child.find('.title').html(data.title);
      $child.find('.search-link').attr('href', data.url);
      $child.find('.date').html(this.formatDate(date));
      $child.find('.excerpt').html(data.excerpt);

      if(data.file_url) {
        $child.find('.file-link').html(data.file_name).attr('href', data.file_url);
        $child.find('.file-size').html(App.tools.formatSize(data.file_size));
        $child.find('.file-length').html(data.file_pages);
      }
      else {
        $child.find('.file').hide();
      }

      return $child;
    },

    getDataObject: function(data) {
      var keywords = this.getSanitizedKeywords();
      var segments = this.getSegmentsFromUrl();
      var page = segments[2] || 1;

      keywords = keywords.replace(/\s+/g, '+');

      var base = {
        'type': '',
        'category': '',
        'keywords': keywords,
        'page': page,
        'resultsPerPage': 10
      };

      if(data) {
        $.each(data, function(key, value) {
          base[key] = value;
        });
      }

      return base;
    },

    parseDate: function(dateString) {
      var dateArray = dateString.split('-');
      if(dateArray.length === 2){
        dateArray.push('01');
      }

      return new Date(dateArray.join('/'));
    },

    formatDate: function(dateObject) {
      return dateObject.getDate()+' '+this.months[dateObject.getMonth()]+' '+dateObject.getFullYear();
    },

    updatePagination: function(data) {
      this.currentPage = parseInt(data.urlParams.page, 10);
      var perPage = parseInt(data.urlParams.per_page, 10);
      var totalResults = parseInt(data.totalResults, 10);
      var totalPages = perPage > 0 ? Math.ceil(totalResults/perPage) : 1;
      var prevPage = Math.max(this.currentPage-1, 1);
      var nextPage = Math.min(this.currentPage+1, totalPages);

      //visibility of the pagination buttons
      this.$prevPage.toggleClass('disabled', this.currentPage <= 1);
      this.$nextPage.toggleClass('disabled', this.currentPage >= totalPages);

      //update data attr used for navigation
      this.$prevPage.attr('data-page', prevPage);
      this.$nextPage.attr('data-page', nextPage);

      //update labels
      this.$prevPage.find('.prev-page').text(prevPage);
      this.$nextPage.find('.next-page').text(nextPage);
      this.$top.find('.total-pages').text(totalPages);
    },

    getSegmentsFromUrl: function() {
      var url = window.location.href;
      var sub = url.substr(this.pageBase.length);
      sub = sub.replace(/^\/|\/$/g, ''); //remove leading and trailing slashes
      return sub.split('/');
    },

    /** Updates the url based on user selections
     */
    updateUrl: function() {
      var urlParts = [this.pageBase];
      var keywords = this.getSanitizedKeywords();

      //type
      urlParts.push(this.$typeInput.val() || 'All');

      //keywords
      keywords = keywords.replace(/\s/g, '+');
      keywords = encodeURI(keywords);
      urlParts.push(keywords || '-');

      //page number
      urlParts.push(this.currentPage);

      if(history.pushState) {
        history.pushState({}, "", urlParts.join('/')+'/');
      }
    }
  };
}(jQuery));
