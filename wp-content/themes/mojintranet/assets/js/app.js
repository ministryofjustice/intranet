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
    },

    inject: (function() {
      var Inject = function(url, callback) {
        this.callback = callback;
        this.loadedCount = 0;

        if(url instanceof Array) {
          this.count = url.length;

          for(var a=0; a<url.length; a++) {
            this.loadScript(url[a]);
          }
        }
        else {
          this.count = 1;
          this.loadScript(url);
        }
      };

      Inject.prototype = {
        loadScript: function(url) {
          var _this = this;
          var script = document.createElement('script');
          script.type = 'text/javascript';
          script.async = true;
          script.onload = function() {
            _this.scriptLoaded();
          };
          script.src = url;
          document.getElementsByTagName('head')[0].appendChild(script);
        },

        scriptLoaded: function() {
          this.loadedCount++;

          if(this.loadedCount >= this.count) {
            if(this.callback) {
              this.callback();
            }
          }
        }
      };

      return function(url, callback) {
        return new Inject(url, callback);
      };
    }()),

    urlencode: function(string) {
      string = encodeURIComponent(string);
      string = string.replace(/%2F/g, '%252F');
      string = string.replace(/%5C/g, '%255C');

      return string;
    },

    urldecode: function(string) {
      string = string.replace(/%252F/g, '%2F');
      string = string.replace(/%255C/g, '%5C');
      string = decodeURIComponent(string);

      return string;
    },

    setCookie: function(name, value, days) {
      var parts = [];
      var date;

      //name=value
      parts.push(encodeURIComponent(name) + "=" + encodeURIComponent(value));

      //expires
      if (days) {
        date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        parts.push("expires=" + date.toGMTString());
      }

      //path
      parts.push('path=/');

      document.cookie = parts.join('; ');
    },

    getCookie: function(name) {
      var cookieNameEq = encodeURIComponent(name) + "=";
      var parts = document.cookie.split(';');
      var part;
      var a;
      var length;

      for (a = 0, length = parts.length; a < length; a++) {
        part = parts[a].replace(/(^\s*|\s*$)/g, '');

        if (part.indexOf(cookieNameEq) === 0) {
          return decodeURIComponent(part.substring(cookieNameEq.length));
        }
      }

      return null;
    },

    deleteCookie: function(name) {
      this.setCookie(name, "", -1);
    }
  };
}(jQuery));

(function($) {
  "use strict";

  var App = window.App;

  App.SkipToContent = function() {
    this.$top = $('.skip-to-content-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.SkipToContent.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
    },

    bindEvents: function() {
      this.$top.on('focus', 'a', $.proxy(this.toggle, this, true));
      this.$top.on('blur', 'a', $.proxy(this.toggle, this, false));
    },

    toggle: function(toggleState) {
      this.$top.toggleClass('focused', toggleState);
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

/**
 * breakpoint.js
 * triggers events on window and changes a class on html element when certain breakpoints are reached
 * it also restricts the amount of resize events getting triggered during resizing which improves performance
 */
(function($) {
  "use strict";

  var App = window.App;

  App.Breakpoint = function() {
    this.settings = {
      delay: 200,
      breakpoints: {
        mobile: 0,
        tablet: 768,
        desktop: 1024
      }
    };

    this.isLocked = false;
    this.unlockHandle = null;
    this.currentBreakpoint = null;

    this.init();
  };

  App.Breakpoint.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
    },

    bindEvents: function() {
      $(window).on('resize', $.proxy(this.resizeHandle, this));
    },

    resizeHandle: function() {
      if(!this.isLocked) {
        this.trigger();
        this.lock();
      }
    },

    trigger: function() {
      var width = window.innerWidth || document.body.clientWidth;
      var breakpointName;

      if(width < this.settings.breakpoints.tablet) {
        this.triggerBreakpoint('mobile', true);
      }

      if(width >= this.settings.breakpoints.tablet && width < this.settings.breakpoints.desktop) {
        this.triggerBreakpoint('tablet', true);
      }

      if(width >= this.settings.breakpoints.desktop) {
        this.triggerBreakpoint('desktop', true);
      }
    },

    triggerBreakpoint: function(breakpointName) {
      var classNames = ['breakpoint-mobile', 'breakpoint-tablet', 'breakpoint-desktop', 'breakpoint-gte-tablet', 'breakpoint-lte-tablet'];
      var $html = $('html');
      var eventName;

      if(this.currentBreakpoint !== breakpointName) {
        $html.removeClass(classNames.join(' '));

        eventName = 'breakpoint-' + breakpointName;
        $(window).trigger(eventName);
        $html.addClass(eventName);

        if(breakpointName === 'tablet' || breakpointName === 'desktop') {
          eventName = 'breakpoint-gte-tablet';
          $(window).trigger(eventName);
          $html.addClass(eventName);
        }

        if(breakpointName === 'tablet' || breakpointName === 'mobile') {
          eventName = 'breakpoint-lte-tablet';
          $(window).trigger(eventName);
          $html.addClass(eventName);
        }

        this.currentBreakpoint = breakpointName;
      }
    },

    lock: function() {
      var _this = this;
      if(this.isLocked) {
        window.clearTimeout(this.unlockHandle);
        this.unlockHandle = null;
      }

      this.isLocked = true;

      this.unlockHandle = window.setTimeout(function() {
        _this.isLocked = false;
        _this.trigger();
      }, this.settings.delay);
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
      this.$childrenPagesJumpBox = $('.children-pages-jump-box');
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
      $link.html(childData.title);
      if(childData.isExternal) {
        $link.attr('rel', 'external');
      }
      $link.appendTo($child);

      return $child;
    },

    updateVisibility: function() {
      var hasChildrenPages = this.$childrenPages.find('li').length > 0;

      this.$childrenPagesBox.toggleClass('visible', hasChildrenPages);
      this.$childrenPagesJumpBox.toggleClass('visible', hasChildrenPages);
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
    this.$myIntranetForm = $('.my-intranet-form');
    if(!this.$myIntranetForm.length) { return; }
    this.init();
  };

  App.DepartmentDropdown.prototype = {
    init: function() {
      this.settings = {
        cookieName: 'department_dropdown'
      };

      this.cacheEls();
      this.bindEvents();
      this.setDropdown();
    },

    cacheEls: function() {
      this.$departmentDropdown = this.$myIntranetForm.find('.department');
      this.$visitCta = this.$myIntranetForm.find('.visit-cta');
    },

    bindEvents: function() {
      this.$myIntranetForm.on('submit', $.proxy(this.visitDepartment, this));
      this.$myIntranetForm.find('.department').on('change', $.proxy(this.saveState, this));
    },

    setDropdown: function() {
      var department = this.readState();
      this.$departmentDropdown.find('[data-department="' + department + '"]').attr('selected', true);
    },

    visitDepartment: function(e) {
      var $form = $(e.currentTarget);
      var selectedDepartmentUrl = $form.closest('.my-intranet-form').find('.department :selected').attr('data-url');

      e.preventDefault();

      if(selectedDepartmentUrl) {
        window.location.href = selectedDepartmentUrl;
      }
    },

    saveState: function(e) {
      var department = $(e.currentTarget).find(':selected').attr('data-department');
      App.tools.setCookie(this.settings.cookieName, department, 3650);
    },

    readState: function() {
      return App.tools.getCookie(this.settings.cookieName);
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

(function($) {
  "use strict";

  var App = window.App;

  App.Feeds = function() {
    this.$top = $('.feeds');
    if(!this.$top.length || App.ie && App.ie < 9) { return; }
    this.init();
  };

  App.Feeds.prototype = {
    init: function() {
      this.scheme = /^http:/.test(window.location.href) ? 'http' : 'https'; //can't we just use scheme-agnostic URL's? to be tested on IE7

      this.initializeTwitter();
      this.initializeYammer();
    },

    initializeTwitter: function() {
      App.tools.inject(this.scheme + '://platform.twitter.com/widgets.js');
    },

    initializeYammer: function() {
      App.tools.inject(this.scheme + '://assets.yammer.com/assets/platform_embed.js', function() {
        window.yam.connect.embedFeed({
          container: '.yammer-feed',
          network: 'justice.gsi.gov.uk',
          feedType: 'group',
          feedId: 'all'
        });

        $('#embed-feed').css({
          height: '600px'
        });
      });
    }
  };
}(window.jQuery));

/* DEBUG ONLY */
/*
(function($) {
  'use strict';

  window.Line = function(color) {
    this.$el = $('<div></div>')
      .appendTo(document.body)
      .css({
        backgroundColor: '#' + color,
        width: '100%',
        height: '1px',
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        zIndex: 99999999999
      })
    ;
  };

  window.Line.prototype.top = function(top) {
    this.$el.css('top', top + 'px');
  };
}(jQuery));
/**/


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
      /* DEBUG ONLY */
      /*
      this.limiterTopLine = new window.Line('00f');
      this.limiterBottomLine = new window.Line('0f0');
      this.maxFloaterTopLine = new window.Line('f00');
      /**/

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
      var _this = this;

      this.$floaters.each(function() {
        var $floater = $(this);
        var $container = $($floater.attr('data-floater-limiter-selector'));
        var floaterHeight = $floater.outerHeight() + 20;
        var limiterHeight = $container.outerHeight();
        var scrollTop = $(window).scrollTop();
        var limiterTop = App.tools.round($container.offset().top, 0);
        var absLimiterBottom = limiterTop + limiterHeight;
        var maxFloaterTop = absLimiterBottom - floaterHeight;

        /* DEBUG ONLY */
        /*
        _this.limiterTopLine.top(limiterTop);
        _this.limiterBottomLine.top(absLimiterBottom);
        _this.maxFloaterTopLine.top(maxFloaterTop);

        console.log({
          absLimiterBottom: absLimiterBottom,
          //scrollTop: scrollTop,
          'scrollTop - limiterTop': scrollTop - limiterTop,
          maxFloaterTop: maxFloaterTop
        });
        /**/

        if(scrollTop > limiterTop) {
          $floater.css({
            marginTop: Math.min(scrollTop, maxFloaterTop) - limiterTop + 'px'
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
    this.$top = $('.template-guidance-and-support-content .template-container');
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
    this.$top = $('.template-guidance-and-support-index .template-container');
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
      var $selectedItem = $container.find('[data-page-id='+categoryId+']');

      e.preventDefault();

      if($selectedItem.hasClass('selected')) {
        return;
      }

      this.markItem($selectedItem);
      this.loadChildren($selectedItem, level+1);

      if(level===1) {
        this.toggleTopLevelCategories(false);
      }
    },

    /** Updates the url based on the selected categories
     */
    updateUrl: function() {
      var $item;
      var urlParts = ['#'];

      this.$columns.each(function() {
        $item = $(this).find('.item.selected');
        if($item.length) {
          urlParts.push($item.data('slug'));
          return true;
        }

        return false;
      });

      window.location.hash = urlParts.join('/') + '/';
    },

    getPartialSegments: function() {
      var partial = document.location.hash;
      return partial.replace(/^\/+|\/+$/g, '').split('/');
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
      var data = {};
      var level = 1;
      var urlParts = this.getPartialSegments();
      var $categoryItem;

      $column = this.$columns.first();
      data = $column.data('items');

      //clean-up
      $column.removeAttr('data-items'); //remove data from the element
      $column.removeAttr('data-selected-id'); //remove data from the element

      this.populateColumn(level, null, data);

      //!!! To be refactored (or killed off when we replace the lemon tree with new nav)
      if(urlParts[level]) {
        $categoryItem = $column.find('[data-slug="' + urlParts[level] + '"]');
        $categoryItem.on('load-children', function() {
          $categoryItem.off('load-children');

          level++;
          if(urlParts[level]) {
            _this.$columns.find('[data-slug="' + urlParts[level] + '"] a').click();
          }
        });
        $categoryItem.find('a').click();
      }
    },

    /** Load children based on category ID
     * @param {Number} categoryId Category ID
     * @param {Number} level Level of the child container [1-3]
     */
    loadChildren: function($selectedItem, level) {
      this.stopLoadingChildren();
      $selectedItem.addClass('loading');
      this.requestChildren($selectedItem, level);
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
    requestChildren: function($selectedItem, level) {
      var _this = this;
      var categoryId = $selectedItem.attr('data-page-id');

      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl+'/'+categoryId, $.proxy(_this.populateColumn, _this, level, $selectedItem));
      //**/}, 2000);
    },

    /** Populates a specified column (based on level) with children specified in data object
     * @param {Number} level Item level (1-3)
     * @param {Object} data Children data
     */
    populateColumn: function(level, $selectedItem, data) {
      var _this = this;
      var $thisLevelContainer = this.$columns.filter('.level-'+level); //this level = the child level
      var $nextLevelContainer = this.$columns.filter('.level-'+(level+1)); //next level = the grandchild level
      var $thisItemList = $thisLevelContainer.find('.item-list');
      var $child;
      var $overviewPageLink;
      var a;

      this.helpers.toggleElement($nextLevelContainer, false);

      //clear all subcolumns
      for(a=level; a<=3; a++) {
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

      if($selectedItem) {
        $selectedItem.trigger('load-children');

        //add overview page link
        $overviewPageLink = this.buildOverviewPageLink(level, $selectedItem);
        $thisItemList.prepend($overviewPageLink);

        //adjust position of the A-Z/Popular
        $thisItemList.prev().css({
          paddingTop: $overviewPageLink.outerHeight() + 'px'
        });

        $thisLevelContainer.find('.item').first().find('a').focus();
      }
    },

    buildOverviewPageLink: function(level, $selectedItem) {
      var $overviewItem = this.buildChild(JSON.parse($selectedItem.attr('data-data')), level);
      var text = $overviewItem.find('.title').text();
      $overviewItem.find('.title').text(text + ' overview');
      $overviewItem.find('.description').html('');
      $overviewItem.off('click');
      $overviewItem.removeAttr('data-name');
      $overviewItem.removeAttr('data-popularity-order');
      $overviewItem.addClass('overview');

      return $overviewItem;
    },

    /** Sets up and returns a child element
     * @param {Object} data Child data model
     * @param {Number} level Item level (1-3)
     * @returns {jQuery} Populated child element
     */
    buildChild: function(data, level) {
      var _this = this;
      var $child = $(this.itemTemplate);
      $child.attr('data-data', JSON.stringify(data));
      $child.attr('data-page-id', data.id);
      $child.attr('data-popularity-order', data.id); //!!! for now the order will be based on IDs
      $child.attr('data-name', data.title);
      $child.attr('data-slug', data.slug);
      $child.find('.title').html(data.title);
      $child.find('a').attr('href', data.url);
      $child.on('click', 'a', $.proxy(this.collapseTopLevelColumn, this, level!==1));
      $child.on('click', 'a', function() {
        $(this).trigger('item-click');
      });

      if(!data.child_count) {
        $child.addClass('no-children');
      }
      if(data.is_external) {
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
        items = $list.find('li:not(.overview)').toArray();
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

/** Mobile header
 */
(function($) {
  "use strict";

  var App = window.App;

  App.MobileHeader = function() {
    this.$top = $('.header');
    if(!this.$top.length) { return; }

    this.config = {
      menuToggleClass: 'menu-opened',
      searchToggleClass: 'search-opened'
    };

    this.init();
  };

  App.MobileHeader.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
      this.$searchInput = this.$top.find('.keywords-field');
      this.$searchButton = this.$top.find('.search-btn');
      this.$menuButton = this.$top.find('.mobile-menu-btn');
      this.$myMoj = this.$top.find('.my-moj');
      this.$appsContainer = this.$myMoj.find('.apps-container');
      this.$quickLinksContainer = this.$myMoj.find('.quick-links-container');
    },

    bindEvents: function() {
      this.$menuButton.on('click', $.proxy(this.toggleMenu, this));
      this.$searchButton.on('click', $.proxy(this.searchClick, this));
      //this.$searchInput.on('blur', $.proxy(this.toggleSearch, this, false));
      $(document).on('click', $.proxy(this.outsideSearchClick, this));
      this.$appsContainer.on('click', '.category-name', $.proxy(this.collapsibleBlockToggle, this));
      this.$quickLinksContainer.on('click', '.category-name', $.proxy(this.collapsibleBlockToggle, this));
    },

    toggleMenu: function(e) {
      this.$top.toggleClass(this.config.menuToggleClass);
    },

    searchClick: function(e) {
      if(!this.$top.hasClass(this.config.searchToggleClass)) {
        e.preventDefault();
        this.toggleSearch(true);
        this.$searchInput.focus();
      }
    },

    toggleSearch: function(toggleState) {
      this.$top.toggleClass(this.config.searchToggleClass, toggleState);
    },

    outsideSearchClick: function(e) {
      if(!$(e.target).closest('.search-form').length) {
        this.toggleSearch(false);
      }
    },

    collapsibleBlockToggle: function(e) {
      var $this = $(e.target);
      var $container = $(e.delegateTarget);

      $container.toggleClass('mobile-collapsed');
    }
  };
}(window.jQuery));

(function($) {
  "use strict";

  App.Navigation = function() {
    this.$top = $('.template-guidance-and-support-content .template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.Navigation.prototype = {
    init: function() {
      this.data = JSON.parse(this.$top.attr('data-children-data'));

      this.cacheEls();
      this.cacheTemplates();
      this.bindEvents();

      this.buildMenu();
    },

    cacheTemplates: function() {
      this.menuItemTpl = $('.template-partial[data-name="menu-item"]').html();
      this.childItemTpl = $('.template-partial[data-name="child-item"]').html();
    },

    cacheEls: function() {
      this.$menu = $('.menu-list');
    },

    bindEvents: function() {
    },

    buildMenu: function() {
      var _this = this;
      var $menuItem;
      var current = false;

      $.each(this.data, function(index, data) {
        $menuItem = _this.buildMenuItem(data);
        $menuItem.appendTo(_this.$menu);
        $menuItem.addClass(index === _this.data.length - 1 ? 'current' : 'collapsed');

        if(!$menuItem.find('.children-list .child-item').length) {
          $menuItem.addClass('no-children');
        }
      });

      this.highlightInAncestors();
    },

    buildMenuItem: function(data) {
      var _this = this;
      var $menuItem = $(this.menuItemTpl);
      var $childrenList = $menuItem.find('.children-list');

      $menuItem.attr('data-id', data.id);
      $menuItem.find('.menu-item-link')
        .attr('href', data.url)
        .html(data.title);

      $menuItem.find('.dropdown-button')
        .click($.proxy(_this.toggle, _this));

      $.each(data.results, function(index, data) {
        _this.buildChildItem(data).appendTo($childrenList);
      });

      return $menuItem;
    },

    buildChildItem: function(data) {
      var $childItem = $(this.childItemTpl);

      $childItem.attr('data-id', data.id);

      $childItem.find('.child-item-link')
        .html(data.title)
        .attr('href', data.url);

      return $childItem;
    },

    clear: function($item) {
      if($item) {
        $item.find('.item-list').empty();
      }
      else {
        this.$menu.empty();
      }
    },

    toggle: function(e) {
      var $element = $(e.target);
      var $item = $element.closest('.menu-item');
      e.preventDefault();

      $item.toggleClass('collapsed');

      this.$menu.find('.menu-item').not('.current').each(function() {
        var $this = $(this);
        if($this.get(0) !== $item.get(0)) {
          $this.addClass('collapsed');
        }
      });
    },

    highlightInAncestors: function() {
      var _this = this;
      var $categoryItem;
      var $link;
      var id;

      this.$menu.find('.menu-item').each(function() {
        $categoryItem = $(this);
        id = $categoryItem.attr('data-id');

        $link = _this.$menu.find('.child-item[data-id="' + id + '"]');
        $link.addClass('highlight');
      });
    }
  };
}(window.jQuery));

/** News
 */
(function($) {
  "use strict";

  var App = window.App;

  App.News = function() {
    this.$top = $('.template-news-landing .template-container');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.News.prototype = {
    init: function() {
      this.settings = {
        dateDropdownLength: 12,
        dateDropdownStartDate: new Date(2015, 0, 1)
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
      this.resultsLoaded = false;

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
        _this.loadResults({
          'page': $(this).attr('data-page')
        });
      });

      this.$nextPage.click(function(e) {
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

      this.clearResults();
      this.setResultsHeading(data);

      $.each(data.results, function(index, result) {
        $newsItem = _this.buildResultRow(result);
        _this.$results.append($newsItem);
      });

      this.updatePagination(data);
      this.updateUrl();
      this.stopLoadingResults();

      this.resultsLoaded = true;
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
        $child.find('.thumbnail')
          .attr('src', data.thumbnail_url)
          .attr('alt', data.thumbnail_alt_text);
      }
      else {
        $child.find('.thumbnail').remove(); //we don't want an img element with no src
      }

      $child.find('.title .news-link').html(data.title);
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

/** Tabbed content
 */
(function($) {
  "use strict";

  var App = window.App;

  App.PageFeedback = function() {
    this.$link = $('.page-feedback-link');
    if(!this.$link.length) { return; }
    this.init();
  };

  App.PageFeedback.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
    },

    cacheEls: function() {
    },

    bindEvents: function() {
      this.$link.click($.proxy(this.prepareEmail, this));
    },

    prepareEmail: function(e) {
      var email = this.$link.attr('href');
      var subject = 'Page feedback - ' + $('title').text();
      var body = [];
      var nl = '\n';

      e.preventDefault();

      body.push(new Array(71).join('-'));
      body.push('This information will help us a lot with resolving the issue. Please do not delete.');
      body.push('Page URL: ' + window.location.href);
      body.push('User agent: ' + window.navigator.userAgent);
      body.push('Screen resolution: ' + window.screen.availWidth + 'x' + window.screen.availHeight);
      body.push(new Array(71).join('-'));

      body = nl + nl + body.join(nl) + nl;

      window.location.href = email + '?subject='+encodeURIComponent(subject)+'&body='+encodeURIComponent(body);
    }
  };
}(jQuery));

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
      var key = e.which || e.keyCode;
      var $highlighted;
      var $target = $(e.target);

      if(key === 38 || key === 40 || key === 27) {
        return;
      }

      $target.attr('data-current-keywords', $target.val());

      this.requestResults($target);
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
        months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
      };

      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl+'/service/search';
      this.pageBase = this.applicationUrl+'/'+this.$top.data('top-level-slug');
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

      this.$keywordsInput.focus();
      this.setFilters();

      this.loadResults();
    },

    cacheEls: function() {
      this.$searchForm = this.$top.find('#search-form');
      this.$typeInput = this.$top.find('[name="type"]');
      this.$categoryInput = this.$top.find('[name="category"]');
      this.$keywordsInput = this.$top.find('.keywords-field');
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
        _this.loadResults({
          'page': $(this).attr('data-page')
        });
      });

      this.$nextPage.click(function(e) {
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
        if(keywords === '-') {
          keywords = '';
        }
        keywords = App.tools.urldecode(keywords);
        keywords = keywords.replace(/\+/g, ' ');

        this.$keywordsInput.val(keywords);
      }
    },

    loadResults: function(requestData) {
      var _this = this;
      var data;

      requestData = this.getDataObject(requestData);

      this.stopLoadingResults();
      this.$top.find('.search-results-title').remove();


      if(this.hasKeywords()) {
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
    },

    setResultsHeading: function(data) {
      var $resultsTitle = $(this.resultsPageTitleTemplate);
      var $filteredResultsTitle = $(this.filteredResultsTitleTemplate);
      var totalResults = parseInt(data.total_results, 10);
      var resultsPage = parseInt(data.url_params.page, 10);
      var date;
      var formattedDate;

      if(this.hasKeywords()) {
        this.$results.append($filteredResultsTitle);
        $filteredResultsTitle.find('.results-count').text(totalResults);
        $filteredResultsTitle.find('.results-count-description').text(totalResults === 1 ? 'result' : 'results');

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
      var _this = this;
      var $child = $(this.itemTemplate);
      var date = this.parseDate(data.timestamp);

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
    updateUrl: function() {
      if(history.pushState) {
        history.pushState({}, "", this.getNewUrl());
      }
    },

    updateGA: function() {
      this.updateGATimeoutHandle = null;
      this.lastSearchUrl = this.getNewUrl(true);

      window.ga('send', 'pageview', this.getNewUrl(true));
    },

    /** Creates and returns as a string a new urls based on current filters
     * @param {Boolean} rootRelative Will only return a root-relative url (omitting the domain)
     * @returns {String} The new url
     */
    getNewUrl: function(rootRelative) {
      var urlParts = [this.pageBase];
      var keywords = this.getSanitizedKeywords();

      //type
      urlParts.push(this.$typeInput.val() || 'All');

      //keywords
      keywords = keywords.replace(/\s/g, '+');
      keywords = App.tools.urlencode(keywords);
      urlParts.push(keywords || '-');

      //page number
      urlParts.push(this.currentPage);

      if(rootRelative) {
        urlParts.shift();
        urlParts.unshift(this.$top.data('top-level-slug'));
        urlParts.unshift(''); //will have a leading slash on the final string (from join)
      }

      return urlParts.join('/')+'/';
    }
  };
}(jQuery));

/** Sticky news
 */
(function($) {
  "use strict";

  var App = window.App;

  App.StickyNews = function() {
    this.$top = $('.news-widget.need-to-know');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.StickyNews.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.showItem(true);
    },

    cacheEls: function() {
      this.$pages = this.$top.find('.news-item');
      this.$pageLinks = this.$top.find('.page');
    },

    bindEvents: function() {
      this.$pageLinks.on('click', 'a', $.proxy(this.showItem, this, false));
    },

    showItem: function(showFirst, e) {
      var pageId;

      if(e) {
        e.preventDefault();
      }

      if(showFirst) {
        pageId = this.$pageLinks.first().attr('data-page-id');
      }
      else {
        pageId = $(e.target).closest('.page').attr('data-page-id');
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
    if(!this.$tableOfContents.length) { return; }
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

jQuery(function() {
  "use strict";

  var App = window.App;

  //Early
  App.ins.breakpoint = new App.Breakpoint();

  //Mid
  App.ins.mobileHeader = new App.MobileHeader();
  App.ins.stickyNews = new App.StickyNews();
  //App.ins.guidanceAndSupport = new App.GuidanceAndSupport();
  App.ins.guidanceAndSupportContent = new App.GuidanceAndSupportContent();
  App.ins.azIndex = new App.AZIndex();
  App.ins.emergencyMessage = new App.EmergencyMessage();
  App.ins.tableOfContents = new App.TableOfContents();
  //App.ins.childrenPages = new App.ChildrenPages();
  App.ins.tabbedContent = new App.TabbedContent();
  App.ins.news = new App.News();
  App.ins.searchResults = new App.SearchResults();
  App.ins.searchAutocomplete = new App.SearchAutocomplete();
  App.ins.floaters = new App.Floaters();
  App.ins.collapsibleBlock = new App.CollapsibleBlock();
  App.ins.departmentDropdown = new App.DepartmentDropdown();
  App.ins.feeds = new App.Feeds();
  App.ins.skipToContent = new App.SkipToContent();
  App.ins.pageFeedback = new App.PageFeedback();
  App.ins.navigation = new App.Navigation();

  //Late
  App.ins.breakpoint.trigger();
});
