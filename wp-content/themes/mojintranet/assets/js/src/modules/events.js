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
      this.$prevPage = this.$top.find('.previous');
      this.$nextPage = this.$top.find('.next');
    },

    bindEvents: function() {
      var _this = this;
      var inputFallbackEvent = (App.ie && App.ie < 9) ? 'keyup' : '';

      this.$keywordsInput.on('input ' + inputFallbackEvent, function(e) {
        _this.resultsRequest({
          page: 1
        });
      });

      this.$dateInput.on('change', function() {
        _this.resultsRequest({
          page: 1
        });
      });

      this.$prevPage.click(function(e) {
        _this.resultsRequest({
          'page': $(this).attr('data-page')
        });
      });

      this.$nextPage.click(function(e) {
        _this.resultsRequest({
          'page': $(this).attr('data-page')
        });
      });
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

      requestData = this.getDataObject(requestData);

      this.resultsAbort();
      this.resultsUpdateUI('loading');

      $.each(requestData, function(key, value) {
        dataArray.push(value);
      });

      this.resultsLoaded = false;

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl+'/'+dataArray.join('/'), $.proxy(_this.resultsDisplay, _this));
      //**/}, 2000);
    },

    resultsUpdateUI: function(status, data) {
      var page = this.getPage();
      var keywords = this.getKeywords();
      var $resultsTitle = $(this.resultsPageTitleTemplate);
      var $filteredResultsTitle = $(this.filteredResultsTitleTemplate);
      var date = this.getDate();
      var humanDate;

      if(status === 'loading') {
        this.$top.find('.results-title').remove();
        this.$top.addClass('loading-results');
        this.$results.prepend($(this.resultsPageTitleTemplate).text('Loading results...'));
        this.$results.find('.results-item').addClass('faded');
      }
      else if(status === 'loaded') {
        this.$top.find('.results-title').remove();
        this.$top.removeClass('loading-results');

        if(keywords.length || date.length) {
          $filteredResultsTitle.find('.results-count').text(data.total_results);
          $filteredResultsTitle.find('.results-count-description').text(data.total_results === 1 ? 'result' : 'results');

          if(keywords.length) {
            $filteredResultsTitle.addClass('with-keywords');
            $filteredResultsTitle.find('.keywords').text(keywords);
          }

          if(date.length) {
            date = this.dateParse(this.getDate());
            humanDate = this.months[date.getMonth()] + ' ' + date.getFullYear();
            $filteredResultsTitle.addClass('with-date');
            $filteredResultsTitle.find('.date').text(humanDate);
          }

          this.$results.prepend($filteredResultsTitle);
        }
        else {
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

      this.resultsLoaded = true;
      this.resultsAbort();
      this.resultsClear();

      $.each(data.results, function(index, result) {
        $eventItem = _this.resultsBuildRow(result);
        _this.$results.append($eventItem);
      });

      this.currentPage = parseInt(data.url_params.page, 10);
      this.paginationUpdate(data);
      this.resultsUpdateUI('loaded', data);
      this.urlUpdate();
    },

    resultsBuildRow: function(data) {
      var $child = $(this.itemTemplate);
      var date = this.dateParse(data.start_date);
      var dayOfWeek = this.weekdays[date.getDay()];
      var month = this.months[date.getMonth()].substr(0, 3);
      var year = date.getFullYear();

      $child.find('.results-link').attr('href', data.url);
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

    paginationUpdate: function(data) {
      var perPage = parseInt(data.url_params.per_page, 10) || 10;
      var totalResults = parseInt(data.total_results, 10);
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

    filtersInit: function() {
      var segments = this.getUrlSegments();
      var keywords;

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
        this.$dateInput.val(segments[2] === '-' ? '' : segments[2]);
      }
    },

    getDate: function() {
      return this.$dateInput.val() || "";
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
      var hashPos = sub.indexOf('#');

      if(hashPos >= 0) {
        sub = sub.substr(0, hashPos);
      }

      sub = sub.replace(/^\/|\/$/g, ''); //remove leading and trailing slashes
      return sub.split('/');
    },

    getPage: function() {
      return parseInt(this.getUrlSegments()[0] || 1, 10);
    },

    urlUpdate: function() {
      var urlParts = [this.pageBase];
      var keywords = this.getKeywords().replace(/\s/g, '+');

      //page number
      urlParts.push(this.currentPage);

      //keywords
      urlParts.push(keywords || '-');

      //date
      urlParts.push(this.getDate() || '-');

      if(history.pushState) {
        history.pushState({}, "", urlParts.join('/')+'/');
      }
    },

    dateParse: function(dateString) {
      var dateArray = dateString.split('-');
      if(dateArray.length === 2){
        dateArray.push('01');
      }

      return new Date(dateArray.join('/'));
    },

    dateFormat: function(dateObject) {
      return dateObject.getDate()+' '+this.months[dateObject.getMonth()]+' '+dateObject.getFullYear();
    }
  };
}(jQuery));
