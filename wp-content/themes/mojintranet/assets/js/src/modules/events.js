/** Events
 */
(function($) {
  "use strict";

  var App = window.App;

  App.Events = function() {
    this.$top = $('.template-events-landing .template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.Events.prototype = {
    init: function() {
      this.settings = {
        dateDropdownLength: 12,
        dateDropdownStartDate: new Date(2015, 0, 1)
      };

      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/events';
      this.pageBase = this.applicationUrl+'/'+this.$top.data('top-level-slug');

      this.itemTemplate = this.$top.find('.template-partial[data-name="events-item"]').html();
      this.resultsPageTitleTemplate = this.$top.find('.template-partial[data-name="events-results-page-title"]').html();
      this.filteredResultsTitleTemplate = this.$top.find('.template-partial[data-name="events-filtered-results-title"]').html();
      this.serviceXHR = null;
      this.months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      this.weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
      this.currentPage = null;
      this.resultsLoaded = false;

      this.cacheEls();
      this.bindEvents();
      this.filtersInit();
      this.resultsRequest();
    },

    cacheEls: function() {
      this.$dateInput = this.$top.find('[name="date"]');
      this.$keywordsInput = this.$top.find('[name="keywords"]');
      this.$results = this.$top.find('.results');
    },

    bindEvents: function() {
    },

    getDataObject: function(data) {
      var base = {
        'date': this.$dateInput.val(),
        'keywords': this.getKeywords().replace(/\s+/g, '+'),
        'page': this.getPage(),
        'resultsPerPage': 2 //commenting out - we want it to use the default setting from the API for now
      };

      if(data) {
        $.each(data, function(key, value) {
          base[key] = value;
        });
      }

      return base;
    },

    resultsRequest: function(requestData) {
      var _this = this;
      var dataArray = [];

      this.resultsAbort();
      this.resultsUpdateUI('loading');

      requestData = this.getDataObject(requestData);

      $.each(requestData, function(key, value) {
        dataArray.push(value);
      });

      this.resultsLoaded = false;

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl+'/'+dataArray.join('/'), $.proxy(_this.resultsDisplay, _this));
      //**/}, 2000);
    },

    resultsUpdateUI: function(status) {
      if(status === 'loading') {
        this.$top.find('.results-title').remove();
        this.$top.addClass('loading-results');
        this.$results.prepend($(this.resultsPageTitleTemplate).text('Loading results...'));
        this.$results.find('.results-item').addClass('faded');
      }
      else if(status === 'loaded') {
        var page = this.getPage();
        var keywords = this.getKeywords();
        var date = this.getDate();
        var $resultsTitle = $(this.resultsPageTitleTemplate);
        var $filteredResultsTitle = $(this.filteredResultsTitleTemplate);

        this.$top.find('.results-title').remove();
        this.$top.removeClass('loading-results');

        if(keywords.length || date.length) {
          if(keywords.length) {
            $filteredResultsTitle.addClass('has-keywords');
          }

          if(date.length) {
            $filteredResultsTitle.addClass('has-date');
          }

          this.$results.prepend($filteredResultsTitle);
        }
        else if(page === 1) { //use 'archive' heading
          this.$results.prepend($resultsTitle.text('Archive'));
        }
        else { //use 'latest' heading
          this.$results.prepend($resultsTitle.text('Latest'));
        }
      }
    },

    resultsAbort: function() {
      this.$top.removeClass('loading-results');

      if(this.serviceXHR) {
        this.serviceXHR.abort();
        this.serviceXHR = null;
      }
    },

    resultsClear: function() {
      this.$results.empty();
    },

    resultsDisplay: function(data) {
      var _this = this;
      var $eventItem;

      this.resultsClear();
      //this.setResultsHeading(data);

      $.each(data.results, function(index, result) {
        $eventItem = _this.resultsBuildRow(result);
        _this.$results.append($eventItem);
      });

      //this.updatePagination(data);
      //this.stopLoadingResults();

      this.resultsUpdateUI('loaded');

      this.resultsLoaded = true;
    },

    resultsBuildRow: function(data) {
      var $child = $(this.itemTemplate);
      var date = this.dateParse(data.start_date);
      var dayOfWeek = this.weekdays[date.getDay()];
      var month = this.months[date.getMonth()].substr(0, 3);
      var year = date.getFullYear();

      $child.find('.date-box').attr('datetime', data.start_date + ' ' + data.start_time);
      $child.find('.date-box .day-of-week').html(dayOfWeek);
      $child.find('.date-box .day-of-month').html(date.getDate());
      $child.find('.date-box .month-year').html(month + ' ' + year);
      $child.find('.title .results-link').html(data.title);
      $child.find('.meta-time .value').html(data.start_time + ' - ' + data.end_time);
      $child.find('.meta-location .value').html(data.location);
      $child.find('.permalink').attr('href', data.url);
      $child.find('.description').html(data.description);

      return $child;
    },

    filtersInit: function() {
      var segments = this.getUrlSegments();
      var keywords;
      var today = new Date();
      var startYear = today.getFullYear();
      var startMonth = today.getMonth();
      var startDay = 1;
      var thisDate;
      var thisYear;
      var thisMonth;
      var thisDay;
      var $option;
      var a;

      for(a=0; a<this.settings.dateDropdownLength; a++) {
        thisDate = new Date(startYear, startMonth - a, startDay);
        thisDay = thisDate.getDate();
        thisMonth = thisDate.getMonth();
        thisYear = thisDate.getFullYear();

        if(new Date(thisYear, thisMonth, thisDay) < this.settings.dateDropdownStartDate) {
          break;
        }

        $option = $('<option>');
        $option.text(this.months[thisMonth] + ' ' + thisYear);
        $option.val(thisYear + '-' + (thisMonth+1));
        this.$dateInput.append($option);
      }

      //set the keywords field
      if(segments[1]) {
        keywords = segments[1].replace('+', ' ');

        //update keywords field with keywords from url
        if(keywords) {
          this.$keywordsInput.val(keywords === '-' ? '' : keywords);
        }
      }

      //set the date field
      if(segments[2]) {
        this.$dateInput.val(segments[2]);
      }
    },

    getDate: function() {
      return this.$dateInput.val();
    },

    getKeywords: function() {
      var keywords = this.$keywordsInput.val();
      keywords = keywords.replace(/^\s+|\s+$/g, '');
      keywords = keywords.replace(/\s+/g, ' ');
      keywords = keywords.replace(/[^a-zA-Z0-9\s]+/g, '');
      return keywords;
    },

    getUrlSegments: function() {
      var url = window.location.href;
      var sub = url.substr(this.pageBase.length);
      sub = sub.replace(/^\/|\/$/g, ''); //remove leading and trailing slashes
      return sub.split('/');
    },

    getPage: function() {
      return parseInt(this.getUrlSegments()[0] || 1, 10);
    },

    urlUpdate: function() {
    },

    dateParse: function(dateString) {
      var dateArray = dateString.split('-');
      if(dateArray.length === 2){
        dateArray.push('01');
      }

      return new Date(dateArray.join('/'));
    },

    dateFormat: function() {
    }
  };
}(jQuery));
