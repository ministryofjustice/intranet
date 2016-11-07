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
        months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
      };

      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/events';
      this.pageBase = this.applicationUrl+'/'+this.$top.data('top-level-slug');

      this.itemTemplate = this.$top.find('.template-partial[data-name="events-item"]').html();
      this.resultsPageTitleTemplate = this.$top.find('.template-partial[data-name="events-results-page-title"]').html();
      this.filteredResultsTitleTemplate = this.$top.find('.template-partial[data-name="events-filtered-results-title"]').html();
      this.serviceXHR = null;
      this.typingTimeout = null;
      this.months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      this.weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
      this.currentPage = null;
      this.resultsLoaded = false;
      this.updateGATimeoutHandle = null;
      this.lastSearchUrl = '';
      this.initialDate = '';

      this.cacheEls();
      this.bindEvents();

      this.populateDateFilter();
      this.filtersInit();
      this.urlUpdate(true);

      this.resultsRequest({
        date: this.initialDate
      });
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
        clearTimeout(this.typingTimeout);
        this.typingTimeout = setTimeout(function() {
          _this.resultsRequest({
            page: 1
          });
        }, 500);
      });

      this.$dateInput.on('change', function() {
        _this.resultsRequest({
          page: 1
        });
      });

      this.$prevPage.click(function(e) {
        e.preventDefault();
        _this.resultsRequest({
          'page': $(this).attr('data-page')
        });
        _this.$top.get(0).scrollIntoView({behavior: 'smooth'});
      });

      this.$nextPage.click(function(e) {
        e.preventDefault();
        _this.resultsRequest({
          'page': $(this).attr('data-page')
        });
        _this.$top.get(0).scrollIntoView({behavior: 'smooth'});
      });

      this.$top.find('.content-filters').submit(function(e) {
        e.preventDefault();
      });

      $(window).on('popstate', function() {
        _this.filtersInit();
        _this.resultsRequest();
      });
    },

    getDataObject: function(data) {
      var base = {
        'agency': App.tools.helpers.agency.getForContent(),
        'additional_filters': '',
        'date': this.$dateInput.val(),
        'keywords': this.getKeywords().replace(/\s+/g, '+'),
        'page': this.getPage()
        //'resultsPerPage': 5 //commenting out - we want it to use the default setting from the API for now
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
      this.resultsUpdateUI('loading', requestData);

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
      var totalResults = parseInt(data.total_results, 10);
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
        else if(!totalResults) {
          $resultsTitle.text('No events found');
          this.$results.append($resultsTitle);
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
      var newUrl;

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
      this.titleUpdate();

      newUrl = this.getNewUrl(true); //must be set after updateUrl

      if(!this.finishedInitialLoad) {
        this.finishedInitialLoad = true;
        this.lastSearchUrl = newUrl;
      }

      if(this.lastSearchUrl !== newUrl) {
        if(this.updateGATimeoutHandle) {
          window.clearTimeout(this.updateGATimeoutHandle);
        }

        this.updateGATimeoutHandle = window.setTimeout($.proxy(this.updateGA, this), this.settings.updateGATimeout);
      }
      else {
        window.clearTimeout(this.updateGATimeoutHandle);
        this.updateGATimeoutHandle = null;
      }
    },

    resultsBuildRow: function(data) {
      var $child = $(this.itemTemplate);
      var startDate = this.dateParse(data.start_date);
      var startDayOfWeek = this.weekdays[startDate.getDay()];
      var startDay = startDate.getDate();
      var startMonth = this.months[startDate.getMonth()];
      var startYear = startDate.getFullYear();
      var endDate = this.dateParse(data.end_date);
      var endDay = endDate.getDate();
      var endMonth = this.months[endDate.getMonth()];
      var endYear = endDate.getFullYear();

      var startDateFormatted = [startDay, startMonth, startYear].join(' ');
      var endDateFormatted = [endDay, endMonth, endYear].join(' ');

      $child.find('.results-link').attr('href', data.url);
      $child.find('.date-box').attr('datetime', data.start_date + ' ' + data.start_time);
      $child.find('.date-box .day-of-week').html(startDayOfWeek);
      $child.find('.date-box .day-of-month').html(startDate.getDate());
      $child.find('.date-box .month-year').html(startMonth.substr(0, 3) + ' ' + startYear);
      $child.find('.title .results-link').html(data.title);
      $child.find('.meta-time .value').html(data.all_day ? 'All day' : data.start_time + ' - ' + data.end_time);
      $child.find('.meta-date .value').html(startDateFormatted + ' - ' + endDateFormatted);
      $child.find('.meta-location .value').html(data.location);
      $child.find('.permalink').attr('href', data.url);
      $child.find('.description').html(data.description);

      if(!data.location) {
        $child.find('.meta-location').addClass('hidden');
      }

      if(data.multiday) {
        $child.find('.meta-time').addClass('hidden');
      }
      else {
        $child.find('.meta-date').addClass('hidden');
      }

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

      this.initialDate = segments[2] === '-' ? '' : segments[2];

      //set the keywords field
      if(segments[1]) {
        keywords = segments[1].replace('+', ' ');

        //update keywords field with keywords from url
        if(keywords) {
          this.$keywordsInput.val(keywords === '-' ? '' : keywords);
        }
      }

      this.currentPage = parseInt(segments[0] || 1, 10);
    },

    populateDateFilter: function() {
      var today = new Date();
      var startYear = today.getFullYear();
      var startMonth = today.getMonth();
      var startDay = 1;
      var paddedMonth;
      var thisDate;
      var thisYear;
      var thisMonth;
      var thisDay;
      var $option;
      var a;

      for(a = 0; a < this.settings.dateDropdownLength; a++) {
        thisDate = new Date(startYear, startMonth + a, startDay);
        thisDay = thisDate.getDate();
        thisMonth = thisDate.getMonth();
        thisYear = thisDate.getFullYear();

        paddedMonth = "" + (thisMonth + 1);
        paddedMonth = paddedMonth.length < 2 ? "0" + paddedMonth : paddedMonth;

        $option = $('<option>');
        $option.text(this.settings.months[thisMonth] + ' ' + thisYear);
        $option.val(thisYear + '-' + paddedMonth);
        this.$dateInput.append($option);
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

    urlUpdate: function(replace) {
      var newUrl = this.getNewUrl();

      if(window.location.href === newUrl) {
        return;
      }

      if(replace) {
        if(history.replaceState) {
          history.replaceState({}, "", newUrl);
        }
      }
      else {
        if(history.pushState) {
          history.pushState({}, "", newUrl);
        }
      }
    },

    titleUpdate: function() {
      var titleParts = ['Events'];
      var keywords = this.getKeywords();

      //keywords
      if (keywords) {
        titleParts.push('including "' + keywords + '"');
      }

      //date
      titleParts.push(this.$dateInput.val() || '');

      //page number
      titleParts.push('(page' + this.currentPage + ')');

      document.title = titleParts.join(' ') + ' - MoJ Intranet';
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
    },

    updateGA: function() {
      this.updateGATimeoutHandle = null;
      this.lastSearchUrl = this.getNewUrl(true);

      window.dataLayer.push({event: 'update-dynamic-content'});
    },

    getNewUrl: function(rootRelative) {
      var urlParts = [this.pageBase];
      var keywords = this.getKeywords().replace(/\s/g, '+');

      //page number
      urlParts.push(this.currentPage);

      //keywords
      urlParts.push(keywords || '-');

      //date
      urlParts.push(this.getDate() || '-');

      if(rootRelative) {
        urlParts.shift();
        urlParts.unshift(this.$top.data('top-level-slug'));
        urlParts.unshift(''); //will have a leading slash on the final string (from join)
      }

      return urlParts.join('/')+'/';
    }
  };
}(jQuery));
