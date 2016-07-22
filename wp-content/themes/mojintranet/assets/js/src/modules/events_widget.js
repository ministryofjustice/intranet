(function($) {
  "use strict";

  var App = window.App;

  App.EventsWidget = function() {
    this.$top = $('.template-home .events-widget');
    if(!this.$top.length || this.$top.hasClass('agency-hidden')) { return; }
    this.init();
  };

  App.EventsWidget.prototype = {
    init: function() {
      this.applicationUrl = $('head').data('application-url');
      this.serviceUrl = this.applicationUrl + '/service/events/' + App.tools.helpers.agency.getForContent() + '////1/2';
      this.pageBase = this.applicationUrl + '/' + this.$top.data('top-level-slug');

      this.itemTemplate = this.$top.find('[data-name="widget-event-item"]').html();
      this.weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
      this.months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

      this.resultsLoaded = false;
      this.serviceXHR = null;

      this.cacheEls();
      this.bindEvents();

      this.requestResults();
    },

    cacheEls: function() {
      this.$postsList = this.$top.find('.events-list');
    },

    bindEvents: function() {
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
      var $post;

      App.ins.skeletonScreens.remove(this.$postsList);

      if (data.results.length > 0) {
        $.each(data.results, function (index, result) {
          $post = _this.buildResultRow(result);
          _this.$postsList.append($post);
        });
      }
      else {
        this.$top.find('.no-events-message').addClass('visible');
        this.$top.addClass('no-events');
      }

      this.resultsLoaded = true;
      this.$top.removeClass('loading');
    },

    buildResultRow: function(data) {
      var $child = $(this.itemTemplate);
      var startDate = App.tools.parseDate(data.start_date);
      var startDayOfWeek = this.weekdays[startDate.getDay()];
      var startDay = startDate.getDate();
      var startMonth = this.months[startDate.getMonth()];
      var startYear = startDate.getFullYear();
      var endDate = App.tools.parseDate(data.end_date);
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
    }
  };
}(jQuery));
