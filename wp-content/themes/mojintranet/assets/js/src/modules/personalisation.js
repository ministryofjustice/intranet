(function($) {
  "use strict";

  var App = window.App;

  App.Personalisation = function() {
    this.init();
  };

  App.Personalisation.prototype = {
    init: function() {
      this.settings = {
        hideForAgency: {
          'hmcts': ['.events-widget', '.events-menu-item']
        }
      };

      this.agency = window.App.tools.helpers.agency.get();


      this.cacheEls();

      this.updateAgencyFromUrl();
      this.addAgencyAttribute();
      this.updateHomepageHeading();
      this.hideContent();
    },

    cacheEls: function() {
      this.$siteHeader = $('body > .header');
    },

    addAgencyAttribute: function() {
      $('html').attr('data-agency', this.agency);
    },

    updateHomepageHeading: function() {
      var $homepage = $('.template-home');
      var agencyData = App.tools.helpers.agency.getData(this.agency);

      if($homepage.length) {
        $('.template-home h1').html(agencyData.label);
      }
    },

    hideContent: function() {
      var selectorsToHide = this.settings.hideForAgency[this.agency] || [];

      $.each(selectorsToHide, function(index, selector) {
        $(selector).addClass('agency-hidden');
      });
    },

    updateAgencyFromUrl: function() {
      var agency = App.tools.getUrlParam('agency');

      if (typeof agency === 'string') {
        App.tools.helpers.agency.set(agency);
        this.removeAgencyFromUrl();
        this.agency = window.App.tools.helpers.agency.get();
      }
    },

    removeAgencyFromUrl: function() {
      var urlParts = window.location.href.split('?');
      var url = urlParts[0];
      var params = App.tools.getUrlParam();
      var newQuery = [];

      if(!window.history.replaceState) {
        return;
      }

      delete params.agency;

      $.each(params, function(key, value) {
        newQuery.push(key + '=' + value);
      });

      if (newQuery.length > 0) {
        url += '?' + newQuery.join('&');
      }

      window.history.replaceState(null, null, url);
    }
  };
}(window.jQuery));
