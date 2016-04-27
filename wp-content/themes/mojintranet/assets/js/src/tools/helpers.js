(function($) {
  "use strict";

  var App = window.App;

  App.tools.helpers = {
    agency: {
      integratedAgencies: ['hmcts', 'laa', 'opg'],

      get: function() {
        return App.tools.getCookie('department_dropdown');
      },

      set: function(agency) {
        App.tools.setCookie('department_dropdown', agency, 3650);
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
