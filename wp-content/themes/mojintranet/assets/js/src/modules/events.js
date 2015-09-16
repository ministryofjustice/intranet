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
      console.log(this.pageBase);

      this.months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

      this.cacheEls();
      this.bindEvents();
      this.filtersInit();
    },

    cacheEls: function() {
      this.$dateInput = this.$top.find('[name="date"]');
      this.$keywordsInput = this.$top.find('[name="keywords"]');
    },

    bindEvents: function() {
    },

    getDataObject: function(data) {
    },

    resultsRequest: function(requestData) {
    },

    resultsAbort: function() {
    },

    resultsClear: function() {
    },

    resultsDisplay: function() {
    },

    resultsBuildRow: function() {
    },

    filtersInit: function() {
      var segments = this.urlGetSegments();
      var keywords;
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
        this.$dateInput.val(segments[2]);
      }
    },

    urlUpdate: function() {
    },

    urlGetSegments: function() {
      var url = window.location.href;
      var sub = url.substr(this.pageBase.length);
      sub = sub.replace(/^\/|\/$/g, ''); //remove leading and trailing slashes
      console.log(sub.split('/'));
      return sub.split('/');
    },

    dateParse: function() {
    },

    dateFormat: function() {
    }
  };
}(jQuery));
