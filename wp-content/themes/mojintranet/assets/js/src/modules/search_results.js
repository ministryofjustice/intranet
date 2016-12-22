/** SearchResults
 */
(function($) {
  "use strict";

  var App = window.App;

  App.SearchResults = function() {
    this.$top = $('.template-search-results .template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.SearchResults.prototype = {
    init: function() {
      this.settings = {
        updateGATimeout: 2000, //timeout for google analytics for search refinements
        minKeywordLength: 2, //not implemented yet
        months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        contentTypeSeparator: '<span class="breadcrumb-separator"></span>',
        postTypes: [ //post types to be added to the page categories dropdown
          {
            'name': 'Blog posts',
            'slug': 'post'
          },
          {
            'name': 'Events',
            'slug': 'event'
          },
          {
            'name': 'News',
            'slug': 'news'
          },
          {
            'name': 'Webchats',
            'slug': 'webchat'
          }
        ]
      };

      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/search';
      this.pageBase = this.applicationUrl+'/'+this.$top.data('top-level-slug');
      this.categories = JSON.parse(this.$top.attr('data-resource-categories'));

      this.itemTemplate = this.$top.find('.template-partial[data-name="search-item"]').html();
      this.resultsPageTitleTemplate = this.$top.find('.template-partial[data-name="search-results-page-title"]').html();
      this.filteredResultsTitleTemplate = this.$top.find('.template-partial[data-name="search-filtered-results-title"]').html();
      this.serviceXHR = null;
      this.updateGATimeoutHandle = null;
      this.currentPage = null;
      this.resultsLoaded = false;
      this.finishedInitialLoad = false;
      this.lastSearchUrl = "";

      this.cacheEls();
      this.bindEvents();

      this.populateCategoryFilter();
      this.setFilters();
      this.$keywordsInput.focus();
      this.updateUrl(true);
      this.loadResults();
    },

    cacheEls: function() {
      this.$searchForm = this.$top.find('.search-form.search-string');
      this.$categoryInputBox = this.$top.find('.resource-categories-box .input-box');
      this.$keywordsInput = this.$top.find('.keywords-field');
      this.$results = this.$top.find('.results');
      this.$prevPage = this.$top.find('.previous');
      this.$nextPage = this.$top.find('.next');
      this.$searchType = this.$top.find('.search-type');
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

      this.$searchForm.on('submit', function(e) {
        e.preventDefault();
      });

      $(window).on('popstate', function() {
        _this.setFilters();
        _this.loadResults();
      });

      this.$searchType.on('change', $.proxy(this.changeSearchType, this));
    },

    changeSearchType: function(e) {
      var $element = $(e.currentTarget).find('option:selected');
      e.preventDefault();

      this.loadResults({
        'type': $element.val(),
        'page': 1
      });
    },

    setFilters: function() {
      var segments = this.getSegmentsFromUrl();
      var type = segments[0] || 'all';
      var keywords;
      var category;

      if(segments[1]) {
        keywords = segments[1];
        if(keywords === '-') {
          keywords = '';
        }
        keywords = App.tools.urldecode(keywords);
        keywords = keywords.replace(/\+/g, ' ');

        this.$keywordsInput.val(keywords);
      }

      if (segments[2]) {
        category = segments[2] || '';

        this.setCategory(category);
      }

      this.currentPage = parseInt(segments[3] || 1, 10);
    },

    populateCategoryFilter: function() {
      var _this = this;
      var $input;
      var $label;
      var agency = App.tools.helpers.agency.getForContent();
      var categoryCount = 0;

      //prepare the post type array to be compatible with the categories array
      $.each(this.settings.postTypes, function(index, postType) {
        postType.isPostType = true;
      });

      //combine and sort the two arrays
      this.categories = this.categories.concat(this.settings.postTypes);
      App.tools.sortByKey(this.categories, 'name');

      //add all categories (and posts) to the select element
      $.each(this.categories, function(index, term) {
        if (App.tools.search(agency, term.agencies) || term.isPostType) {
          //type and name must be defined inline, not with attr() cos IE7...
          $input = $('<input type="radio" name="resource-category">')
            .attr('data-is-post-type', term.isPostType ? 1 : 0)
            .val(term.slug)
            .on('change', function() {
              _this.loadResults({
                page: 1
              });
            });

          $label = $('<label>')
            .attr('class', 'block-label')
            .html(term.name);

          $input.prependTo($label);
          $label.appendTo(_this.$categoryInputBox.find('.fields'));
          categoryCount++;
        }
      });

      if (!categoryCount) {
        this.$top.find('.resource-categories-box').addClass('hidden');
      }
    },

    loadResults: function(requestData) {
      var data;
      var keywords = this.getSanitizedKeywords();

      requestData = this.getDataObject(requestData);

      this.stopLoadingResults();
      this.$top.find('.search-results-title').remove();

      if(this.hasKeywords() && keywords.length >= 2) {
        this.$top.addClass('loading-results');
        this.$results.prepend($(this.resultsPageTitleTemplate).text('Loading results...'));

        this.$results.find('.search-item').addClass('faded');

        this.requestResults(requestData);
      }
      else {
        this.clearResults();

        data = {
          results: [],
          total_results: 0,
          url_params: {
            category: null,
            keywords: "",
            page: "1",
            per_page: "10",
            type: "page"
          }
        };

        this.updatePagination(data);
        this.updateUrl();
        this.updateTitle();
        this.setResultsHeading(data);
      }
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

      data.keywords = App.tools.urlencode(data.keywords);

      $.each(data, function(key, value) {
        dataArray.push(value);
      });

      this.resultsLoaded = false;

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl+'/'+dataArray.join('/'), $.proxy(_this.displayResults, _this));
      //**/}, 5000);
    },

    clearResults: function() {
      this.$results.empty();
    },

    displayResults: function(data) {
      var _this = this;
      var $searchItem;
      var newUrl;

      this.clearResults();
      this.setResultsHeading(data);

      $.each(data.results, function(index, result) {
        $searchItem = _this.buildResultRow(result);
        _this.$results.append($searchItem);
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
      var keywords = this.getSanitizedKeywords();

      if(keywords.length < 2) {
        this.$results.append($filteredResultsTitle);
        $filteredResultsTitle.find('h3').hide();
        $filteredResultsTitle.find('.no-keywords-info').removeClass('hidden');
      }
      else if(this.hasKeywords()) { //has keywords but there were no results
        this.$results.append($filteredResultsTitle);
        $filteredResultsTitle.find('.results-count').text(totalResults);
        $filteredResultsTitle.find('.results-count-description').text('search ' + (totalResults === 1 ? 'result' : 'results'));

        if(!totalResults) {
          $filteredResultsTitle.find('.no-results-info').removeClass('hidden');
        }
      }
    },

    hasKeywords: function() {
      var keywords = this.getSanitizedKeywords();
      var keywordsArray = keywords.split(' ');

      if(!keywords.length){ return false; }

      return true;
    },

    getSanitizedKeywords: function() {
      var keywords = this.$keywordsInput.val();
      keywords = keywords.replace(/\s+/g, ' ');
      keywords = keywords.replace(/^\s+|\s+$/g, '');

      return keywords;
    },

    buildResultRow: function(data) {
      var $child = $(this.itemTemplate);
      var date = this.parseDate(data.modified_timestamp);

      if(data.thumbnail_url) {
        $child.find('.thumbnail').attr('src', data.thumbnail_url);
      }
      else {
        $child.find('.thumbnail').remove(); //we don't want an img element with no src
      }

      $child.find('.search-link').html(data.title);
      $child.find('.search-link').attr('href', data.url);
      $child.find('.date').html(this.formatDate(date));
      $child.find('.excerpt').html(data.excerpt);
      $child.find('.type').html(data.content_type.join(this.settings.contentTypeSeparator));

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

    getCategory: function() {
      return this.$top.find('[name="resource-category"]:checked').val();
    },

    setCategory: function(category) {
      this.$top.find('[name="resource-category"][value="' + category + '"]').prop('checked', true);
    },

    getDataObject: function(data) {
      var _this = this;
      var keywords = this.getSanitizedKeywords();
      var segments = this.getSegmentsFromUrl();
      var page = segments[3] || 1;
      var category = this.getCategory() || '';
      var postTypes = [];
      var additionalFilters = [];
      var base = {};
      var isPostType;

      if (category) {
        isPostType = this.$top.find('[name="resource-category"][value="' + category + '"][data-is-post-type="1"]').length;

        if (isPostType) {
          postTypes = [category];
        }
        else {
          postTypes = ['page', 'document'];
          additionalFilters.push('resource_category=' + category);
        }
      }
      else { //if nothing is selected then include everything
        postTypes = ['all'];
      }

      base = {
        'agency': App.tools.helpers.agency.getForContent(),
        'additional_filters': additionalFilters.join('&'),
        'type': postTypes.join('|'),
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
      return dateObject.getDate()+' '+this.settings.months[dateObject.getMonth()]+' '+dateObject.getFullYear();
    },

    updatePagination: function(data) {
      this.currentPage = parseInt(data.url_params.page, 10);
      var perPage = parseInt(data.url_params.per_page, 10);
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
      var url = window.location.href;
      var sub = url.substr(this.pageBase.length);
      sub = sub.replace(/^\/|\/$/g, ''); //remove leading and trailing slashes
      return sub.split('/');
    },

    /** Updates the url based on user selections
     */
    updateUrl: function(replace) {
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

    updateTitle: function() {
      var titleParts = [];
      var keywords = this.getSanitizedKeywords();
      var type = this.$searchType.find('option:selected').val();

      //type
      titleParts.push((type || 'All') + ' search results');

      //keywords
      if (keywords) {
        titleParts.push('for "' + keywords + '"');
      }

      //page number
      titleParts.push('(page ' + this.currentPage + ')');

      document.title = titleParts.join(' ') + ' - MoJ Intranet';
    },

    updateGA: function() {
      this.updateGATimeoutHandle = null;
      this.lastSearchUrl = this.getNewUrl(true);

      window.dataLayer.push({event: 'update-dynamic-content'});
    },

    /** Creates and returns as a string a new urls based on current filters
     * @param {Boolean} rootRelative Will only return a root-relative url (omitting the domain)
     * @returns {String} The new url
     */
    getNewUrl: function(rootRelative) {
      var urlParts = [this.pageBase];
      var keywords = this.getSanitizedKeywords();
      var category = this.getCategory() || '';
      var type = this.$searchType.find('option:selected').val();

      //type
      urlParts.push('all');

      //keywords
      keywords = keywords.replace(/\s/g, '+');
      keywords = App.tools.urlencode(keywords);
      urlParts.push(keywords || '-');

      //categories
      urlParts.push(category || '-');

      //page number
      urlParts.push(this.currentPage);

      if(rootRelative) {
        urlParts.shift();
        urlParts.unshift(this.$top.data('top-level-slug'));
        urlParts.unshift(''); //will have a leading slash on the final string (from join)
      }

      return urlParts.join('/')+'/';
    },

    //checks whether the supplied post type exists on the list of post types
    isPostType: function(postType) {
      var a, length;

      for (a = 0, length = this.settings.postTypes.length; a < length; a++) {
        if (this.settings.postTypes[a].slug === postType) {
          return true;
        }
      }

      return false;
    },
  };
}(jQuery));
