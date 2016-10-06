(function($) {
  "use strict";

  var App = window.App;

  App.Personalisation = function() {
    if (App.tools.helpers.agency.agencies) {
      this.init();
    }
  };

  App.Personalisation.prototype = {
    init: function() {
      this.settings = {
        hideForAgency: {
          'hmcts': ['.events-widget', '.main-nav-events', '.posts-widget'],
          'laa': ['.events-widget', '.main-nav-events']
        }
      };

      this.agency = window.App.tools.helpers.agency.get();
      this.agencyData = window.App.tools.helpers.agency.getData();

      this.cacheEls();

      this.setAgency();
      this.addAgencyAttribute();
      this.initializeMenu();
      this.updateLogo();

      this.updateHomepageHeading();
      this.updateSearchPlaceholder();

      this.$header.addClass('loaded');

      this.hideContent();
    },

    cacheEls: function() {
      this.$mainLogoBox = $('.site-logo');
      this.$smallLogoBox = $('.site-logo-hq');
      this.$header = $('.header');
    },

    addAgencyAttribute: function() {
      $('html').attr('data-agency', this.agency);
    },

    initializeMenu: function() {
      var $menu = $('.header-menu');

      if (this.agencyData.blog_url) {
        $menu.find('.main-nav-blog a').attr('href', this.agencyData.blog_url);
      }
    },

    updateLogo: function() {
      var agency = window.App.tools.helpers.agency.get();
      var $logo = this.$mainLogoBox.find('img');
      var agencyData = window.App.tools.helpers.agency.getData();
      var isIntegrated = window.App.tools.helpers.agency.isIntegrated();
      var agencyImgSrc = $logo.attr('src').replace('moj_logo', 'moj_logo_' + agency);

      if (agency !== 'hq' && isIntegrated) {
        $logo
          .attr('src', agencyImgSrc)
          .attr('alt', agencyData.label + ' logo');

        this.$smallLogoBox.addClass('visible');
      }

      this.$mainLogoBox.addClass('visible');

    },

    updateHomepageHeading: function() {
      var agencyData = App.tools.helpers.agency.getData(this.agency);
      var $homepage = $('.template-home');
      var $homeHeading = $('.template-home h1');

      if ($homepage.length) {
        $homeHeading.html(agencyData.label);
      }
    },

    updateSearchPlaceholder: function() {
      var $keywordsField = $('.search-form .keywords-field');

      $keywordsField.attr('placeholder', 'Search ' + this.agencyData.abbreviation + ' intranet');
    },

    hideContent: function() {
      var selectorsToHide = this.settings.hideForAgency[this.agency] || [];

      $.each(selectorsToHide, function(index, selector) {
        $(selector).addClass('agency-hidden');
      });

      this.fixMenuForIE();
    },

    fixMenuForIE: function() {
      var $menuItems = $('.header-menu .main-menu-item:visible');
      var count = $menuItems.length;

      if (count > 0 && App.ie && App.ie <= 9) {
        $menuItems.css({
          width: '' + 100/count + '%'
        });
      }
    },

    setAgency: function() {
      var agency = App.tools.url(true).param('agency');
      var agencyTools = App.tools.helpers.agency;

      //set agency from url
      if (typeof agency === 'string') {
        agencyTools.set(agency);
        this.removeAgencyFromUrl();
      }

      //set default agency in cookie if missing
      if (!agencyTools.getCookie()) {
        agencyTools.set(agencyTools.getForContent());
      }

      this.agency = agencyTools.get();
    },

    removeAgencyFromUrl: function() {
      var url = App.tools.url(true);

      if (!window.history.replaceState) {
        return;
      }

      url.unsetParam('agency');

      window.history.replaceState(null, null, url.get());
    }
  };
}(window.jQuery));
