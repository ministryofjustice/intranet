(function($) {
  "use strict";

  var App = window.App;

  App.NeedToKnowWidget = function() {
    this.$top = $('.template-home .need-to-know-widget');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.NeedToKnowWidget.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl + '/service/widgets/need-to-know/';
      this.pageBase = this.applicationUrl + '/' + this.$top.data('top-level-slug');

      this.itemTemplate = this.$top.find('[data-name="widget-need-to-know-item"]').html();

      this.resultsLoaded = true;
      this.serviceXHR = null;

      this.$slides = [];

      this.cacheEls();
      this.bindEvents();

      this.requestResults();
    },

    cacheEls: function() {
      this.$slidesList = this.$top.find('.slide-list');
      this.$leftNav = this.$top.find('.left-arrow');
      this.$rightNav = this.$top.find('.right-arrow');
      this.$pageIndicator = this.$top.find('.need-to-know-page-indicator');
    },

    bindEvents: function() {
      this.$leftNav.on('click', $.proxy(this.changeSlide, this, true));
      this.$rightNav.on('click', $.proxy(this.changeSlide, this, false));
    },

    requestResults: function() {
      var _this = this;
      var dataArray = [];

      /* use the timeout for dev/debugging purposes */
      //**/window.setTimeout(function() {
        _this.serviceXHR = $.getJSON(_this.serviceUrl+'/'+dataArray.join('/'), $.proxy(_this.displayResults, _this));
      //**/}, 2000);
    },

    displayResults: function(data) {
      var _this = this;
      var $slide;

      $.each(data.results, function(index, result) {
        if(result.url && result.image_url && result.title) { // Prevent incomplete slides
          $slide = _this.buildResultRow(result);
          _this.$slidesList.append($slide);
        }
      });

      this.$slides = this.$slidesList.find('.results-item');
      this.markSlideAsActive(this.$slides.first());

      this.resultsLoaded = true;
      this.$top.removeClass('loading');
    },

    buildResultRow: function(data) {
      var $child = $(this.itemTemplate);

      $child.find('.need-to-know-link').attr('href', data.url);
      $child.find('.need-to-know-image').attr('src', data.image_url);
      $child.find('.need-to-know-image').attr('alt', data.image_alt);
      $child.find('.need-to-know-title').html(data.title);

      return $child;
    },

    changeSlide: function(isBack, e) {
      var $currentSlide = this.$slidesList.find('.current-slide');
      var $newSlide;

      e.preventDefault();

      if(isBack) {
        $newSlide = $currentSlide.prev();

        if(!$newSlide.length) {
          $newSlide = this.$slides.last();
        }
      }
      else {
        $newSlide = $currentSlide.next();

        if(!$newSlide.length) {
          $newSlide = this.$slides.first();
        }
      }

      this.markSlideAsActive($newSlide);
    },

    markSlideAsActive: function($element) {
      var slideIndex = this.$slides.index($element) || 0;

      this.$slides.removeClass('current-slide');
      $element.addClass('current-slide');

      this.$pageIndicator.html((slideIndex + 1) + '/' + this.$slides.length);
    }
  };
}(jQuery));
