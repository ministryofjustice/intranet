(function($) {
  "use strict";

  var App = window.App;

  App.tools.helpers = {
    agency: {
      cookieName: 'dw_agency',
      integratedAgencies: ['hmcts', 'laa', 'opg'],

      get: function() {
        return this.getCookie() || 'hq';
      },

      set: function(agency) {
        App.tools.setCookie(this.cookieName, agency, 3650);
        $(window).trigger('agency-changed');
      },

      getCookie: function() {
        return App.tools.getCookie(this.cookieName);
      },

      getForContent: function() {
        return this.isIntegrated() ? this.get() : 'hq';
      },

      isIntegrated: function() {
        var agency = this.get();

        return this.integratedAgencies.indexOf(agency) >= 0;
      }
    }
  };
}(jQuery));
