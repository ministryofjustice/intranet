(function($) {
  "use strict";

  var App = window.App;

  App.Personalisation = function() {
    this.init();
  };

  App.Personalisation.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();

      this.update();
    },

    cacheEls: function() {
      this.$siteHeader = $('body > .header');
    },

    bindEvents: function() {
    },

    update: function() {
      var agency = window.App.tools.helpers.agency.get();
      var $homepage = $('.template-home');
      var $departmentDropdown = $('.department-list');
      var name;

      if(App.tools.helpers.agency.isIntegrated(agency)) {
        name = $departmentDropdown.find('[data-department="' + agency + '"]').text();
      }
      else {
        name = $departmentDropdown.find('[data-department="hq"]').text();
      }

      $('html').attr('data-agency', agency);

      if($homepage.length) {
        $('.template-home h1').text(name);
      }
    }
  };
}(window.jQuery));
