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
          'hmcts': ['.events-widget', '.main-nav-events', '.main-nav-guidance', '.main-nav-about-us'],
          'laa': ['.events-widget', '.main-nav-events']
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
    },

    addAgencyAttribute: function() {
      $('html').attr('data-agency', this.agency);
    },

    updateHomepageHeading: function() {
      var agencyData = App.tools.helpers.agency.getData(this.agency);
      var $homepage = $('.template-home');
      var $homeHeading = $('.template-home h1');
      var $agencyLinkList = $('.agency-link-list');

      if($homepage.length) {
        $homeHeading.html(agencyData.label);

        $agencyLinkList.toggleClass('hidden', agencyData.url === '');
        $agencyLinkList.find('.agency').attr('data-department', this.agency);
        $agencyLinkList.find('a').attr('href', agencyData.url);
        $agencyLinkList.find('.label').html(agencyData.label);
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
