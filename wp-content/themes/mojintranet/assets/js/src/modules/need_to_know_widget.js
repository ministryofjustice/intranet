(function($) {
  "use strict";

  var App = window.App;

  App.NeedToKnowWidget = function(data) {
    this.data = data;
    this.$top = $('.template-home .need-to-know-widget');
    if(!this.$top.length) { return; }
    this.init();
  };

  App.NeedToKnowWidget.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.pageBase = this.applicationUrl + '/' + this.$top.data('top-level-slug');

      this.itemTemplate = this.$top.find('[data-name="widget-need-to-know-item"]').html();

      this.resultsLoaded = true;
      this.serviceXHR = null;

      this.$slides = [];

      this.cacheEls();
      this.bindEvents();

      this.displayResults(this.data);
    },

    cacheEls: function() {
      this.$slidesList = this.$top.find('.slide-list');
      this.$slideNav = this.$top.find('.need-to-know-pagination');
      this.$leftNav = this.$top.find('.left-arrow');
      this.$rightNav = this.$top.find('.right-arrow');
    },

    bindEvents: function() {
      this.$leftNav.on('click', $.proxy(this.changeSlide, this, true));
      this.$rightNav.on('click', $.proxy(this.changeSlide, this, false));
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

      var randomSlide = Math.floor(Math.random()*this.$slides.length);

      this.markSlideAsActive(this.$slides.eq(randomSlide));

      if(this.$slides.length===0) {
        this.$top.addClass('hidden');
      }

      if(this.$slides.length===1) {
        this.$slideNav.addClass('hidden');
      }

      this.resultsLoaded = true;
      this.$top.removeClass('loading');
    },

    buildResultRow: function(data) {
      var $child = $(this.itemTemplate);

      $child.find('.need-to-know-link').attr('href', data.url);
      $child.find('.need-to-know-image').attr('src', data.image_url);
      $child.find('.need-to-know-image').attr('alt', data.image_alt);
      $child.find('.need-to-know-title .need-to-know-link').html(data.title);

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
    }
  };
}(jQuery));
