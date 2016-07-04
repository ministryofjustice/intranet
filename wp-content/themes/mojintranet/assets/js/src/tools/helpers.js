(function($) {
  "use strict";

  var App = window.App;

  App.tools.helpers = {
    agency: {
      cookieName: 'dw_agency',
      agencies: (function() {
        var $header = $('body > .header');
        var data;
        var agenciesStr = $header.attr('data-agencies');

        if (agenciesStr) {
          data = JSON.parse(agenciesStr);
        }

        $header.removeAttr('data-agencies');

        return data;
      }()),

      get: function() {
        return this.getCookie() || 'hq';
      },

      set: function(agency) {
        App.tools.setCookie(this.cookieName, agency, 3650);
        $(window).trigger('agency-changed');
      },

      getData: function(agency) {
        return this.agencies[agency || this.get()];
      },

      getCookie: function() {
        return App.tools.getCookie(this.cookieName);
      },

      getForContent: function() {
        return this.isIntegrated() ? this.get() : 'hq';
      },

      isIntegrated: function() {
        var agency = this.get();

        return !!this.agencies[agency].is_integrated;
      }
    }
  };
}(jQuery));
