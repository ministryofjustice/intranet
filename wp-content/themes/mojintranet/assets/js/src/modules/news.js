/** News
 */
(function($) {
  "use strict";

  var App = window.App;

  App.News = function() {
    var $html = $('html');

    if ($html.hasClass('template-news-landing')) {
      this.newsType = 'global';
    }
    else if($html.hasClass('template-regional-updates-landing')) {
      this.newsType = 'regional';
    }
    else {
      return;
    }

    this.$top = $('.template-container');
    this.init();
  };

  App.News.prototype = {
    init: function() {
      this.settings = {
        dateDropdownLength: 12,
        dateDropdownStartDate: new Date(2015, 0, 1),
        months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
      };

      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/news/get';
      this.pageBase = this.$top.data('page-base-url');

      this.itemTemplate = this.$top.find('.template-partial[data-name="news-item"]').html();
      this.resultsPageTitleTemplate = this.$top.find('.template-partial[data-name="news-results-page-title"]').html();
      this.filteredResultsTitleTemplate = this.$top.find('.template-partial[data-name="news-filtered-results-title"]').html();
      this.genericThumbnailPath = this.$top.data('template-uri') + '/assets/images/news-placeholder.jpg';
      this.serviceXHR = null;
      this.updateGATimeoutHandle = null;
      this.currentPage = null;
      this.resultsLoaded = false;
      this.finishedInitialLoad = false;
      this.lastSearchUrl = "";

      this.cacheEls();
      this.bindEvents();

      this.initialiseCurrentPage();

      if (this.newsType === 'global') {
        this.populateDateFilter();
        this.populateCategoryFilter();
        this.setFilters();
      }

      this.updateUrl(true);

      this.loadResults();
    },

    cacheEls: function() {
      this.$dateInput = this.$top.find('[name="date"]');
      this.$categoryInput = this.$top.find('[name="categories[]"]');
      this.$keywordsInput = this.$top.find('[name="keywords"]');
      this.$results = this.$top.find('.results');
      this.$prevPage = this.$top.find('.previous');
      this.$nextPage = this.$top.find('.next');
    },

    bindEvents: function() {
      var _this = this;
      var inputFallbackEvent = (App.ie && App.ie < 9) ? 'keyup' : '';
      var typingTimeout;

      this.$keywordsInput.on('input ' + inputFallbackEvent, function(e) {
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(function() {
          _this.loadResults({
            page: 1
          });
        }, 500);
      });

      this.$dateInput.on('change', function() {
        _this.loadResults({
          page: 1
        });
      });

      this.$categoryInput.on('change', function() {
        _this.loadResults({
          page: 1
        });
      });

      this.$prevPage.click(function(e) {
        e.preventDefault();
        _this.loadResults({
          'page': $(this).attr('data-page')
        });
        _this.$top.get(0).scrollIntoView({behavior: 'smooth'});
      });

      this.$nextPage.click(function(e) {
        e.preventDefault();
        _this.loadResults({
          'page': $(this).attr('data-page')
        });
        _this.$top.get(0).scrollIntoView({behavior: 'smooth'});
      });

      this.$top.find('.content-filters').submit(function(e) {
        e.preventDefault();
      });

      $(window).on('popstate', function() {
        _this.setFilters();
        _this.loadResults();
      });
    },

    populateDateFilter: function() {
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
        $option.text(this.settings.months[thisMonth] + ' ' + thisYear);
        $option.val(thisYear + '-' + (thisMonth+1));
        this.$dateInput.append($option);
      }
    },

    populateCategoryFilter: function() {
      var _this = this;
      var categoriesStr = this.$top.attr('data-news-categories');
      var categories = categoriesStr ? JSON.parse(this.$top.attr('data-news-categories')) : {};
      var $option;
      var agency = App.tools.helpers.agency.getForContent();
      var categoryCount = 0;

      $.each(categories, function(index, term) {
        if (App.tools.search(agency, term.agencies)) {
          $option = $('<option></option>')
            .val(term.slug)
            .html(term.name)
            .appendTo(_this.$categoryInput);
          categoryCount++;
        }
      });

      if (!categoryCount) {
        this.$top.find('.news-categories-box').addClass('hidden');
      }
    },

    setFilters: function() {
      var segments = this.getSegmentsFromUrl();
      var keywords;
      var categories;

      if (segments[2]) {
        keywords = segments[2].replace('+', ' ');

        //update keywords field with keywords from url
        if (keywords) {
          this.$keywordsInput.val(keywords === '-' ? '' : keywords);
        }
      }

      //update date field with date from url
      if (segments[3]) {
        this.$dateInput.val(segments[3]);
      }

      if (segments[4]) {
        categories = segments[4].split('|') || [];
        this.$categoryInput.val(categories);
      }

      App.ins.multiSelect.replace(this.$categoryInput);
    },

    initialiseCurrentPage: function() {
      var segments = this.getSegmentsFromUrl();

      this.currentPage = parseInt(segments[1] || 1, 10);
    },

    loadResults: function(requestData) {
      //appending the title below seems redundant as we remove all ".results-title" elements further down anyway...
      var $title = this.$top.find('.results-page-title');

      if(!$title.length) {
        $title = $(this.resultsPageTitleTemplate);
        this.$results.append($title);
      }

      requestData = this.getDataObject(requestData);

      this.stopLoadingResults();
      this.$top.addClass('loading-results');

      this.$top.find('.results-title').remove();
      this.$results.prepend($(this.resultsPageTitleTemplate).text('Loading results...'));

      this.$results.find('.results-item').addClass('faded');

      this.requestResults(requestData);
    },

    stopLoadingResults: function() {
      this.$top.removeClass('loading-results');
      this.$top.find('.news-group-separator.loading'); // ??? redundant
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

      this.resultsLoaded = false;

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
      var $newsItem;
      var newUrl;

      this.clearResults();
      this.setResultsHeading(data);

      $.each(data.results, function(index, result) {
        $newsItem = _this.buildResultRow(result);
        _this.$results.append($newsItem);
      });

      this.updatePagination(data);
      this.updateUrl();
      this.updateTitle();
      this.stopLoadingResults();

      this.resultsLoaded = true;

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

      window.App.ins.accessibility.updateDocLinks(this.$results);
    },

    setResultsHeading: function(data) {
      var $resultsTitle = $(this.resultsPageTitleTemplate);
      var $filteredResultsTitle = $(this.filteredResultsTitleTemplate);
      var totalResults = parseInt(data.total_results, 10);
      var resultsPage = parseInt(data.url_params.page, 10);
      var date;
      var formattedDate;

      if(this.hasKeywords() || this.$dateInput.val()) {
        this.$results.append($filteredResultsTitle);
        $filteredResultsTitle.find('.results-count').text(totalResults);
        $filteredResultsTitle.find('.results-count-description').text('search ' + (totalResults === 1 ? 'result' : 'results'));

        if(this.hasKeywords()) {
          $filteredResultsTitle.find('.keywords').text(this.getSanitizedKeywords());
        }
        else {
          $filteredResultsTitle.find('.containing').hide();
          $filteredResultsTitle.find('.keywords').hide();
        }

        if(this.$dateInput.val()) {
          date = this.parseDate(this.$dateInput.val());
          formattedDate = this.settings.months[date.getMonth()] + ' ' + date.getFullYear();
          $filteredResultsTitle.find('.date').text(formattedDate);
        }
        else {
          $filteredResultsTitle.find('.for-date').hide();
          $filteredResultsTitle.find('.date').hide();
        }
      }
      else if(!totalResults) {
        $resultsTitle.text('No news found');
        this.$results.append($resultsTitle);
      }
      else {
        $resultsTitle.text(resultsPage === 1 ? 'Latest' : 'Archive');
        this.$results.append($resultsTitle);
      }
    },

    hasKeywords: function() {
      return this.getSanitizedKeywords().length > 0;
    },

    getSanitizedKeywords: function() {
      var keywords = this.$keywordsInput.val() || '';
      keywords = keywords.replace(/^\s+|\s+$/g, '');
      keywords = keywords.replace(/\s+/g, ' ');
      keywords = keywords.replace(/[^a-zA-Z0-9\s]+/g, '');
      return keywords;
    },

    buildResultRow: function(data) {
      var $child = $(this.itemTemplate);
      var date = this.parseDate(data.timestamp);

      if(!data.thumbnail_url) {
        data.thumbnail_url = this.genericThumbnailPath;
        data.thumbnail_alt_text = 'generic news thumbnail';
      }

      $child.find('.thumbnail')
        .attr('src', data.thumbnail_url)
        .attr('alt', data.thumbnail_alt_text);

      $child.find('.title .results-link').html(data.title);
      $child.find('.results-link').attr('href', data.url);
      $child.find('.date').html(this.formatDate(date));
      $child.find('.excerpt').html(data.excerpt);

      return $child;
    },

    getDataObject: function(data) {
      var keywords = this.getSanitizedKeywords();
      var segments = this.getSegmentsFromUrl();
      var categories = this.$categoryInput.val();
      var additionalFilters = $.type(categories) === 'array' ? 'news_category=' + categories.join('|') : '';

      keywords = keywords.replace(/\s+/g, '+');

      var base = {
        'agency': App.tools.helpers.agency.getForContent(),
        'additional_filters': additionalFilters,
        'date': this.$dateInput.val(),
        'keywords': keywords,
        'page': segments[1] || 1
        //'resultsPerPage': 20 //commenting out - we want it to use the default setting from the API for now
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
      return dateObject.getDate()+' '+this.settings.months[dateObject.getMonth()]+' '+dateObject.getFullYear();
    },

    updatePagination: function(data) {
      this.currentPage = parseInt(data.url_params.page, 10);
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

    getSegmentsFromUrl: function() {
      var sub = window.location.href.substr(this.pageBase.length);
      sub = App.tools.trim(sub, '/');
      return sub.split('/');
    },

    /** Updates the url based on user selections
     */
    updateUrl: function(replace) {
      var newUrl = this.getNewUrl(true);

      if(window.location.pathname === newUrl) {
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

    /** Updates the page title based on user selections
     */
    updateTitle: function() {
      var titleParts = ['News'];
      var keywords = this.getSanitizedKeywords();

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

    updateGA: function() {
      this.updateGATimeoutHandle = null;
      this.lastSearchUrl = this.getNewUrl(true);

      window.dataLayer.push({event: 'update-dynamic-content'});
    },

    getNewUrl: function(rootRelative) {
      var keywords = this.getSanitizedKeywords();
      var categories = this.$categoryInput.val() || [];
      var rootRelativeBaseUrl = App.tools.trim(this.pageBase.substr(this.applicationUrl.length), '/');
      var urlParts = rootRelativeBaseUrl.split('/');

      keywords = keywords.replace(/\s/g, '+');

      //page number
      urlParts.push('page');
      urlParts.push(this.currentPage);

      if (this.newsType === 'global') {
        //keywords
        urlParts.push(keywords || '-');

        //date
        urlParts.push(this.$dateInput.val() || '-');

        //categories
        urlParts.push(categories.join('|') || '-');
      }

      if(rootRelative) {
        urlParts.unshift(''); //a small trick to add a leading slash
      }
      else {
        urlParts.unshift(this.applicationUrl);
      }

      return urlParts.join('/') + '/';
    }
  };
}(jQuery));
