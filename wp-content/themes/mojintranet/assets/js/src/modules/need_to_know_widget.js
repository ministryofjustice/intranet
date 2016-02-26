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

      this.cacheEls();
      this.bindEvents();

      this.requestResults();
    },

    cacheEls: function() {
      this.$slidesList = this.$top.find('.slide-list');
      this.$leftNav = this.$top.find('.left-arrow');
      this.$rightNav = this.$top.find('.right-arrow');
    },

    bindEvents: function() {
      this.$leftNav.on('click', $.proxy(this.$top.find('.left-arrow'), this));
      this.$rightNav.on('click', $.proxy(this.changeSlide, this));
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
        $slide = _this.buildResultRow(result);
        _this.$slidesList.append($slide);
      });

      _this.$slidesList.find('li').first().addClass('current-slide');

      var totalSlides = _this.$slidesList.find('li').size();

      _this.$top.find('.need-to-know-page-indicator').html('1/' + totalSlides);

      this.resultsLoaded = true;
      this.$top.removeClass('loading');
    },

    buildResultRow: function(data) {
      var $child = $(this.itemTemplate);

      if(data.url && data.image_url && data.title) { // Prevent incomplete slides
        $child.find('.need-to-know-link').attr('href', data.url);
        $child.find('.need-to-know-image').attr('src', data.image_url);
        $child.find('.need-to-know-title').html(data.title);

        return $child;
      }
    },

    changeSlide: function(isBack) {
      var $currentSlide = this.$slidesList.find('li .current-slide');

      if(isBack) {
        $currentSlide.prev().addClass('current-slide');
      } else {
        $currentSlide.next().addClass('current-slide');
      }
      $currentSlide.removeClass('current-slide');
    }
  };
}(jQuery));
