(function($) {
  "use strict";

  window.App = {
    tools: {},
    ins: {}
  };

  App.ie = null;

  (function() {
    var $html = $('html');

    if($html.hasClass('ie7')) {
      App.ie = 7;
    }
    else if($html.hasClass('ie8')) {
      App.ie = 8;
    }
    else if($html.hasClass('ie9')) {
      App.ie = 9;
    }
  }());

  //App.ie = 7; //for debugging purposes
}(jQuery));

/** App tools - generic set of tools used across the whole application
 */
(function($) {
  "use strict";

  var App = window.App;

  var settings = {
    sizeUnits: ['B', 'KB', 'MB', 'GB', 'TB', 'PB']
  };

  App.tools = {
    /** Rounds a number with a specified precision
     * @param {Number} num Input number
     * @param {Number} precision Number of decimal places
     * @returns {Number} Rounded number
     */
    round: function(num, precision) {
      var p;

      if(!precision){
        return Math.round(num);
      }

      p = (precision) ? Math.pow(10, precision) : 1;
      return Math.round(num*p)/p;
    },

    /** Formats data size
     * @param {Number} size Input size in bytes
     * @returns {String} Formatted size (e.g. 103.4KB)
     */
    formatSize: function(size) {
      var level = 0;

      while(size >= 1024) {
        size = App.tools.round(size / 1024, 2);
        level++;
      }

      return (level > 0 ? this.round(size, 2) : size) + settings.sizeUnits[level];
    }
  };
}(jQuery));

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

/** Children pages
 * Note: it's designed to work with only one instance per page
 */
(function($) {
  "use strict";

  var App = window.App;

  App.ChildrenPages = function() {
    this.settings = {
      serviceUrl: '/service/children/'
    };

    this.$childrenPages = $('.children-pages');
    this.$pageContainer = $('.guidance-and-support-content');
    this.pageId = this.$pageContainer.attr('data-page-id');
    if(!this.$childrenPages.length || !this.pageId) { return; }
    this.init();
  };

  App.ChildrenPages.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.getChildren();
    },

    cacheEls: function() {
      this.$childrenPagesBox = $('.children-pages-box');
    },

    bindEvents: function() {
    },

    getChildren: function() {
      var _this = this;

      $.ajax({
        url: this.settings.serviceUrl + this.pageId,
        type: 'json',
        success: function(data) {
          _this.populateChildrenList(data);
        },
        error: function() {
        }
      });
    },

    populateChildrenList: function(data) {
      var _this = this;
      var $child;

      $.each(data.items, function(index, child) {
        $child = _this.constructChildLink(child);
        $child.appendTo(_this.$childrenPages);
      });

      this.updateVisibility();
    },

    constructChildLink: function(childData) {
      var $child = $('<li></li>');
      var $link = $('<a></a>');

      $link.attr('href', childData.url);
      $link.text(childData.title);
      if(childData.isExternal) {
        $link.attr('rel', 'external');
      }
      $link.appendTo($child);

      return $child;
    },

    updateVisibility: function() {
      this.$childrenPagesBox.toggleClass('visible', this.$childrenPages.find('li').length > 0);
    }
  };
}(jQuery));

/** Sticky news
 */
(function($) {
  "use strict";

  var App = window.App;

  App.CollapsibleBlock = function() {
    this.$toggleLinks = $('.collapsible-block-toggle');
    if(!this.$toggleLinks.length) { return; }
    this.init();
  };

  App.CollapsibleBlock.prototype = {
    init: function() {
      this.bindEvents();
    },

    bindEvents: function() {
      var _this = this;
      this.$toggleLinks.on('click', $.proxy(this.toggleList, this));
    },

    toggleList: function(e) {
      var $toggle = $(e.target);
      var $container = $toggle.closest('.collapsible-block-container');
      var openedLabel = $toggle.attr('data-opened-label');
      var closedLabel = $toggle.attr('data-closed-label');

      e.preventDefault();

      $container.toggleClass('opened');

      if($.type(openedLabel)!=='undefined' && $.type(closedLabel)!=='undefined') {
        $toggle.text($container.hasClass('opened') ? openedLabel : closedLabel);
      }
    }
  };
}(window.jQuery));

(function($) {
  "use strict";

  var App = window.App;

  App.DepartmentDropdown = function() {
    this.$departmentDropdownBox = $('.department-dropdown-box');
    if(!this.$departmentDropdownBox.length) { return; }
    this.init();
  };

  App.DepartmentDropdown.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$departmentDropdown = this.$departmentDropdownBox.find('.department');
    },

    bindEvents: function() {
      this.$departmentDropdown.on('change keyup', $.proxy(this.changeDepartmentHandle, this));
      //$(window).on('DOMContentLoaded load', $.proxy(this.changeDepartmentHandle, this));
    },

    changeDepartmentHandle: function() {
      var url = this.$departmentDropdown.find('option:selected').attr('data-url');
      window.location.href = url;

      //var deptName = this.$departmentDropdown.find('option:selected').attr('data-department');
      //this.$departmentDropdownBox.attr('data-department', deptName);
    }
  };
}(window.jQuery));

/** Emergency message
 */
(function($) {
  "use strict";

  var App = window.App;

  App.EmergencyMessage = function() {
    this.$top = $('.message');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.EmergencyMessage.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$closeButton = this.$top.find('.close');
    },

    bindEvents: function() {
      this.$closeButton.on('click', $.proxy(this.close, this));
    },

    close: function() {
      this.$top.slideUp(200);
    }
  };
}(window.jQuery));

/** Floaters
 * Handles floating elements which have minimum and maximum position determined by its container
 */
(function($) {
  "use strict";

  var App = window.App;

  App.Floaters = function() {
    this.$floaters = $('.js-floater');
    if(!this.$floaters.length) { return; }
    this.init();
  };

  App.Floaters.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.setUpFloaters();
    },

    cacheEls: function() {
    },

    bindEvents: function() {
      $(window).on('scroll', $.proxy(this.scrollHandler, this));
    },

    setUpFloaters: function() {
      this.$floaters.each(function() {
        var $floater = $(this);
        $floater.attr('data-start-position', App.tools.round($floater.offset().top, 0));
      });
    },

    scrollHandler: function() {
      this.$floaters.each(function() {
        var $floater = $(this);
        var $container = $($floater.attr('data-floater-limiter-selector'));
        var floaterHeight = $floater.outerHeight();
        var limiterTop = App.tools.round($container.offset().top, 0);
        var limiterHeight = $container.outerHeight();
        var scrollTop = $(window).scrollTop();

        if(scrollTop > limiterTop) {
          $floater.css({
            marginTop: Math.min(scrollTop - limiterTop, limiterHeight - floaterHeight - 100) + 'px'
          });
        }
        else {
          $floater.css({
            marginTop: '' //restore original
          });
        }
      });
    }
  };
}(jQuery));

/** Guidance and Support content template
 */
(function($) {
  "use strict";

  var App = window.App;

  App.GuidanceAndSupportContent = function() {
    this.$top = $('.guidance-and-support-content');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.GuidanceAndSupportContent.prototype = {
    init: function() {
      this.redirectUrl = this.$top.attr('data-redirect-url');
      this.redirectEnabled = this.$top.attr('data-redirect-enabled');
      this.isImported = this.$top.attr('data-is-imported');

      if(this.redirectUrl && this.redirectEnabled==="1") {
        this.redirect(this.redirectUrl);
      }
    },

    redirect: function(url) {
      window.location.href = url;
    }
  };
}(window.jQuery));

/** Guidance and Support page index
 */
(function($) {
  "use strict";

  var App = window.App;

  App.GuidanceAndSupport = function() {
    this.$top = $('.guidance-and-support');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.GuidanceAndSupport.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/children';
      this.pageBase = this.applicationUrl+'/'+this.$top.data('top-level-slug');

      this.itemTemplate = this.$top.find('.template-partial[data-name="guidance-and-support-category-item"]').html();
      this.serviceXHR = null;

      this.cacheEls();
      this.bindEvents();

      this.prepopulateColumns();
    },

    cacheEls: function() {
      this.$tree = this.$top.find('.tree');
      this.$columns = this.$tree.find('.item-container');

      this.$sortList = this.$top.find('.tabbed-filters');
      this.$sortPopular = this.$sortList.find('[data-sort-type="popular"]');
      this.$sortAlphabetical = this.$sortList.find('[data-sort-type="alphabetical"]');

      this.$allCategoriesLink = this.$tree.find('.all-categories');
    },

    bindEvents: function() {
      var _this = this;

      this.$sortAlphabetical.on('click', 'a', function(e) {
        e.preventDefault();
        _this.sort('alphabetical');
        _this.$sortList.find('.filter-item').removeClass('selected');
        $(this).parent().addClass('selected');
      });

      this.$sortPopular.on('click', 'a', function(e) {
        e.preventDefault();
        _this.sort('popular');
        _this.$sortList.find('.filter-item').removeClass('selected');
        $(this).parent().addClass('selected');
      });

      this.$allCategoriesLink.on('click', function(e) {
        e.preventDefault();
        _this.toggleTopLevelCategories(true);
        _this.collapseTopLevelColumn(false);
      });

      if(!App.ie) {
        this.$tree.hammer().on('swipeleft', $.proxy(this.swipeMobileColumns, this, 'left'));
        this.$tree.hammer().on('swiperight', $.proxy(this.swipeMobileColumns, this, 'right'));
      }
      $(document).on('keydown', $.proxy(this.swipeMobileColumns, this, null));
    },

    /** Switches between A-Z columns on mobile view
     */
    swipeMobileColumns: function(direction, e) {
      var currentColumn;
      var newColumn;

      if(e.type==='keydown') {
        if(e.keyCode===37) { direction = 'right'; }
        else if(e.keyCode===39) { direction = 'left'; }
        else { return; }
      }

      currentColumn = parseInt(this.$tree.attr('data-show-column'), 10);
      newColumn = (direction==='left') ? currentColumn+1 : currentColumn-1;

      if(this.$columns.filter('.level-'+newColumn).is(':visible')) {
        this.$tree.attr('data-show-column', newColumn);
      }

      //console.log('direction: '+direction+', currentColumn: '+currentColumn + ', newColumn: '+newColumn+ ', applying: '+this.$columns.filter('.level-'+newColumn).is(':visible'));
    },

    /** Performs tasks after user clicks on a category link
     * @param {Number} categoryId Category ID
     * @param {Number} level Level number (1-3)
     * @param {Event} e Event
     */
    categoryClick: function(categoryId, level, e) {
      var $container = this.$columns.filter('.level-'+level);
      var $parent = $container.find('[data-page-id='+categoryId+']');

      e.preventDefault();

      if($parent.hasClass('selected')) {
        return;
      }

      this.markItem($parent);
      this.loadChildren(categoryId, level+1);

      if(level===1) {
        this.toggleTopLevelCategories(false);
      }
    },

    /** Updates the url based on the selected categories
     */
    updateUrl: function() {
      var $item;
      var urlParts = [this.pageBase];

      this.$columns.each(function() {
        $item = $(this).find('.item.selected');
        if($item.length) {
          urlParts.push($item.data('slug'));
          return true;
        }

        return false;
      });

      if(history.pushState) {
        history.pushState({}, "", urlParts.join('/')+'/');
      }
    },

    /** Marks a specified item as selected
     * @param {jQuery} item Item element
     */
    markItem: function($item) {
      $item.closest('.item-list').find('.selected').removeClass('selected');
      $item.addClass('selected');
    },

    /** Toggles all top-level categories
     * @param {Boolean} toggle Whether to show or hide categories
     */
    toggleTopLevelCategories: function(toggle) {
      if(toggle===undefined) {
        throw new Error('toggle parameter must be set (boolean)');
      }

      this.$columns.filter('.level-1').find('.item:not(.selected)').slideToggle(toggle);
      this.$allCategoriesLink.toggleClass('visible', !toggle);
    },

    /** Collapses the level 1 column (CSS-controlled)
     * @param {Boolean} toggle Toggle state
     */
    collapseTopLevelColumn: function(toggle) {
      this.$tree.toggleClass('collapsed', toggle);
    },

    /** Populates all columns based on data properties generated server-side
     */
    prepopulateColumns: function() {
      var _this = this;
      var $column;
      var selectedId;
      var data = {};
      var level = 0;

      this.$columns.each(function() {
        $column = $(this);
        data = $column.data('items');
        selectedId = $column.data('selected-id');

        //clean-up
        $column.removeAttr('data-items'); //remove data from the element
        $column.removeAttr('data-selected-id'); //remove data from the element

        if(data) {
          level++;
          _this.populateColumn(level, data);
          _this.markItem($column.find('[data-page-id="'+selectedId+'"]'));
        }
      });

      if(level>1) {
        this.toggleTopLevelCategories(false);
      }
      if(level>2) {
        this.collapseTopLevelColumn();
      }
    },

    /** Load children based on category ID
     * @param {Number} categoryId Category ID
     * @param {Number} level Level of the child container [1-3]
     */
    loadChildren: function(categoryId, level) {
      this.stopLoadingChildren();
      this.$tree.find('[data-page-id="'+categoryId+'"]').addClass('loading');
      this.requestChildren(categoryId, level);
    },

    /** Stops loading children elements
     */
    stopLoadingChildren: function() {
      this.$tree.find('.item.loading').removeClass('loading');
      if(this.serviceXHR) {
        this.serviceXHR.abort();
        this.serviceXHR = null;
      }
    },

    /** Performs an XHR to get children and adds the children to the relevant column
     * @param {Number} categoryId ID of the category we request the children of
     * @param {Number} level Level of the child container [1-3]
     */
    requestChildren: function(categoryId, level) {
      var _this = this;

      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl+'/'+categoryId, $.proxy(_this.populateColumn, _this, level));
      //**/}, 2000);
    },

    /** Populates a specified column (based on level) with children specified in data object
     * @param {Number} level Item level (1-3)
     * @param {Object} data Children data
     */
    populateColumn: function(level, data) {
      var _this = this;
      var $thisLevelContainer = this.$columns.filter('.level-'+level); //this level = the child level
      var $nextLevelContainer = this.$columns.filter('.level-'+(level+1)); //next level = the grandchild level
      var $thisItemList = $thisLevelContainer.find('.item-list');
      var $child;
      var a;

      this.helpers.toggleElement($nextLevelContainer, false);

      //clear all subcolumns
      for(a=level;a<=3;a++) {
        this.$columns.filter('.level-'+a).find('.item-list').empty();
      }

      if(level>1) {
        $thisLevelContainer.find('.category-name').html(data.title);
      }

      $.each(data.items, function(index, item) {
        $child = _this.buildChild(item, level);
        $thisItemList.append($child);
      });

      this.sort();
      this.helpers.toggleElement($thisLevelContainer, true);
      this.stopLoadingChildren();
      this.$columns.removeClass('current');
      this.$columns.filter('.level-'+level).addClass('current');

      this.updateUrl();


      this.$tree.attr('data-show-column', level);
    },

    /** Sets up and returns a child element
     * @param {Object} data Child data model
     * @param {Number} level Item level (1-3)
     * @returns {jQuery} Populated child element
     */
    buildChild: function(data, level) {
      var _this = this;
      var $child = $(this.itemTemplate);
      $child.attr('data-page-id', data.id);
      $child.attr('data-popularity-order', data.id); //!!! for now the order will be based on IDs
      $child.attr('data-name', data.title);
      $child.attr('data-slug', data.slug);
      $child.find('.title').html(data.title);
      $child.find('a').attr('href', data.url);
      $child.on('click', 'a', $.proxy(this.collapseTopLevelColumn, this, level!==1));

      if(data.is_external && !data.child_count) {
        $child.find('a').attr('rel', 'external');
      }

      if(level<3 && data.child_count>0) {
        $child.find('.description').html(data.excerpt);
        $child.on('click', 'a', $.proxy(_this.categoryClick, _this, data.id, level));
      }

      return $child;
    },

    /** Sorts items in all columns alphabetically or by popularity
     * @param {String} type Type of sort [alphabetical/popular]
     */
    sort: function(type) {
      var level;
      var items;
      var sortMethod;
      var sortLabel;
      var $list;

      if(!type) {
        type = this.$sortList.find('.selected').data('sort-type');
      }

      sortMethod = type==='alphabetical' ? this.helpers.alphabeticalComparator : this.helpers.popularComparator;
      sortLabel = type==='alphabetical' ? 'A to Z' : 'Popular';

      for(level=1; level<=3; level++) {
        $list = this.$columns.filter('.level-'+level).find('.item-list');
        items = $list.find('li').toArray();
        items.sort(sortMethod);
        $list.append(items);
      }

      //update sort labels
      this.$tree.find('.sort-order').text(sortLabel);
    },

    helpers: {
      /** Sort comparator for alphabetical order
       */
      alphabeticalComparator: function(a, b) {
        var label1 = $(a).data('name');
        var label2 = $(b).data('name');
        return (label1 < label2) ? -1 : (label1 > label2) ? 1 : 0;
      },

      /** Sort comparator for popular order
       */
      popularComparator: function(a, b) {
        var label1 = $(a).data('popularity-order');
        var label2 = $(b).data('popularity-order');
        return (label1 < label2) ? -1 : (label1 > label2) ? 1 : 0;
      },

      /** Generic method which toggles classes indicating visibility on an element
       * N.B. due to the generic nature of this method it could and should be moved
       * to become a part of the app toolkit (doesn't exist yet)
       * @param {jQuery} $element Subject element
       * @param {Boolean} toogle Toggle state
       */
      toggleElement: function($element, toggle) {
        $element.toggleClass('visible', toggle);
        $element.toggleClass('hidden', !toggle);
      }
    }
  };
}(window.jQuery));

/** Mobile menu
 */
(function($) {
  "use strict";

  var App = window.App;

  App.MobileMenu = function() {
    this.$top = $('.header');
    if(!this.$top.length) { return; }

    this.config = {
      menuToggleClass: 'mobile-menu-enabled'
    };

    this.init();
  };

  App.MobileMenu.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$menuButton = this.$top.find('.mobile-nav button');
    },

    bindEvents: function() {
      this.$top.on('click', 'button', $.proxy(this.toggleMenu, this));
    },

    toggleMenu: function() {
      this.$top.toggleClass(this.config.menuToggleClass);
    }
  };
}(window.jQuery));

/** News
 */
(function($) {
  "use strict";

  var App = window.App;

  App.News = function() {
    this.$top = $('.page-news');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.News.prototype = {
    init: function() {
      this.settings = {
        dateDropdownMonths: 12
      };

      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/news';
      this.pageBase = this.applicationUrl+'/'+this.$top.data('top-level-slug');

      this.itemTemplate = this.$top.find('.template-partial[data-name="news-item"]').html();
      this.resultsPageTitleTemplate = this.$top.find('.template-partial[data-name="news-results-page-title"]').html();
      this.filteredResultsTitleTemplate = this.$top.find('.template-partial[data-name="news-filtered-results-title"]').html();
      this.serviceXHR = null;
      this.months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      this.currentPage = null;

      this.cacheEls();
      this.bindEvents();

      this.populateDateFilter();
      this.setFilters();

      this.loadResults();
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
        _this.loadResults({
          page: 1
        });
      });

      this.$dateInput.on('change', function() {
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
    },

    populateDateFilter: function() {
      var today = new Date();
      var startYear = today.getFullYear();
      var startMonth = today.getMonth();
      var startDay = 1;
      var thisDate;
      var thisYear;
      var thisMonth;
      var $option;
      var a;

      for(a=0; a<this.settings.dateDropdownMonths; a++) {
        thisDate = new Date(startYear, startMonth - a, startDay);
        thisMonth = thisDate.getMonth();
        thisYear = thisDate.getFullYear();
        $option = $('<option>');
        $option.text(this.months[thisMonth] + ' ' + thisYear);
        $option.val(thisYear + '-' + (thisMonth+1));
        this.$dateInput.append($option);
      }
    },

    setFilters: function() {
      var segments = this.getSegmentsFromUrl();
      var keywords;

      if(segments[2]) {
        keywords = segments[2].replace('+', ' ');

        //update keywords field with keywords from url
        if(keywords) {
          this.$keywordsInput.val(keywords === '-' ? '' : keywords);
        }
      }

      //update date field with date from url
      if(segments[3]) {
        this.$dateInput.val(segments[3]);
      }
    },

    loadResults: function(requestData) {
      var _this = this;
      var $title = this.$top.find('.news-results-page-title');

      if(!$title.length) {
        $title = $(this.resultsPageTitleTemplate);
        this.$results.append($title);
      }

      requestData = this.getDataObject(requestData);

      this.stopLoadingResults();
      this.$top.addClass('loading-results');

      this.$top.find('.news-results-title').remove();
      this.$results.prepend($(this.resultsPageTitleTemplate).text('Loading results...'));

      this.$results.find('.news-item').addClass('faded');

      this.requestResults(requestData);
    },

    stopLoadingResults: function() {
      this.$top.removeClass('loading-results');
      this.$top.find('.news-group-separator.loading');
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
      var $newsItem;

      this.clearResults();
      this.setResultsHeading(data);

      $.each(data.results, function(index, result) {
        $newsItem = _this.buildResultRow(result);
        _this.$results.append($newsItem);
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

      if(this.hasKeywords() || this.$dateInput.val()) {
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

        if(this.$dateInput.val()) {
          date = this.parseDate(this.$dateInput.val());
          formattedDate = this.months[date.getMonth()] + ' ' + date.getFullYear();
          $filteredResultsTitle.find('.date').text(formattedDate);
        }
        else {
          $filteredResultsTitle.find('.for-date').hide();
          $filteredResultsTitle.find('.date').hide();
        }
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
      var keywords = this.$keywordsInput.val();
      keywords = keywords.replace(/^\s+|\s+$/g, '');
      keywords = keywords.replace(/\s+/g, ' ');
      keywords = keywords.replace(/[^a-zA-Z0-9\s]+/g, '');
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
      $child.find('.news-link').attr('href', data.url);
      $child.find('.date').html(this.formatDate(date));
      $child.find('.excerpt').html(data.excerpt);

      return $child;
    },

    getDataObject: function(data) {
      var keywords = this.getSanitizedKeywords();
      var segments = this.getSegmentsFromUrl();
      var page = segments[1] || 1;

      keywords = keywords.replace(/\s+/g, '+');

      var base = {
        'category': '',
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
      keywords = keywords.replace(/\s/g, '+');

      //page number
      urlParts.push('page');
      urlParts.push(this.currentPage);

      //keywords
      urlParts.push(keywords || '-');

      //date
      urlParts.push(this.$dateInput.val() || '-');

      if(history.pushState) {
        history.pushState({}, "", urlParts.join('/')+'/');
      }
    }
  };
}(jQuery));

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
      var inputFallbackEvent = (App.ie && App.ie < 9) ? 'keyup' : '';

      this.$keywordsInput.on('input ' + inputFallbackEvent, function(e) {
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

/** Sticky news
 */
(function($) {
  "use strict";

  var App = window.App;

  App.StickyNews = function() {
    this.$top = $('#need-to-know');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.StickyNews.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.showItem(1);
    },

    cacheEls: function() {
      this.$pages = this.$top.find('.need-to-know-list > li');
      this.$pageLinks = this.$top.find('.page-list > li');
    },

    bindEvents: function() {
      this.$pageLinks.on('click', $.proxy(this.showItem, this, null));
    },

    showItem: function(pageId, e) {
      if(!pageId) {
        pageId = $(e.target).data('page-id');
      }

      this.$pages.hide();
      this.$pageLinks.removeClass('selected');
      this.$pages.filter('[data-page="'+pageId+'"]').show();
      this.$pageLinks.filter('[data-page-id="'+pageId+'"]').addClass('selected');
    }
  };
}(window.jQuery));

/** Tabbed content
 */
(function($) {
  "use strict";

  var App = window.App;

  App.TabbedContent = function() {
    this.$tabs = $('.content-tabs li');
    if(!this.$tabs.length) { return; }
    this.init();
  };

  App.TabbedContent.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.cacheTemplates();
      this.$tabs.eq(0).click();
    },

    cacheEls: function() {
      this.$tabContent = $('.tab-content');
    },

    cacheTemplates: function() {
      var _this = this;

      this.templates = [];

      $('.template-partial[data-template-type]').each(function() {
        var $el = $(this);
        _this.templates[$el.attr('data-content-name')] = $el.html();
      });
    },

    bindEvents: function() {
      this.$tabs.on('click', $.proxy(this.switchTab, this));
    },

    switchTab: function(e) {
      var $el = $(e.currentTarget);
      var contentName = $el.attr('data-content');
      this.$tabContent.html(this.templates[contentName]);
      this.$tabs.removeClass('current-menu-item');
      $el.addClass('current-menu-item');
      e.preventDefault();

      //hopefully one day we can replace this manual call with Mutation Observer
      App.ins.tableOfContents.generate();
    }
  };
}(jQuery));

/** Table of contents
 * Note: it's designed to work with only one instance per page
 */
(function($) {
  "use strict";

  var App = window.App;

  App.TableOfContents = function() {
    this.$tableOfContents = $('.table-of-contents');
    this.isImported = !!$('.guidance-and-support-content[data-is-imported="1"]').length;
    if(!this.$tableOfContents.length || this.isImported) { return; }
    this.init();
  };

  App.TableOfContents.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.generate();
      this.initialized = true;
    },

    cacheEls: function() {
      this.$tableOfContentsBox = $('.table-of-contents-box');
      this.$contentContainer = $(this.$tableOfContents.attr('data-content-selector'));
    },

    bindEvents: function() {
    },

    generate: function() {
      var _this = this;

      if(!this.initialized) { return; }

      this.$tableOfContents.empty();
      //find all H2 tags with ID's
      this.$contentContainer.find('h2').each(function() {
        var $el = $(this);
        var $item = $('<li><a></a></li>');
        var attr;

        if(!$el.filter('[id]').length) {
          attr = $el.text().toLowerCase();
          attr = attr.replace(/[^A-Za-z0-9\s-]/g, '');
          attr = attr.replace(/[\s+]/g, '-');
          $el.attr('id', attr);
        }

        $item.find('a')
          .text($el.text())
          .attr('href', '#'+$el.attr('id'));
        $item.appendTo(_this.$tableOfContents);
      });

      this.updateBoxVisibility();
    },

    updateBoxVisibility: function() {
      this.$tableOfContentsBox.toggleClass('visible', this.$tableOfContents.find('> li').length > 0);
    }
  };
}(jQuery));

jQuery(function($) {
  "use strict";

  var App = window.App;

  App.ins.mobileMenu = new App.MobileMenu();
  App.ins.stickyNews = new App.StickyNews();
  App.ins.guidanceAndSupport = new App.GuidanceAndSupport();
  App.ins.guidanceAndSupportContent = new App.GuidanceAndSupportContent();
  App.ins.azIndex = new App.AZIndex();
  App.ins.emergencyMessage = new App.EmergencyMessage();
  App.ins.tableOfContents = new App.TableOfContents();
  App.ins.childrenPages = new App.ChildrenPages();
  App.ins.tabbedContent = new App.TabbedContent();
  App.ins.news = new App.News();
  App.ins.searchResults = new App.SearchResults();
  App.ins.floaters = new App.Floaters();
  App.ins.collapsibleBlock = new App.CollapsibleBlock();
  App.ins.departmentDropdown = new App.DepartmentDropdown();
});
