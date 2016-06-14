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
          'hmcts': ['.events-widget', '.main-nav-events', '.main-nav-guidance', '.main-nav-about-us', '.posts-widget'],
          'laa': [/*'.events-widget', '.main-nav-events'*/]
        }
      };

      this.agency = window.App.tools.helpers.agency.get();
      this.agencyData = window.App.tools.helpers.agency.getData();

      this.cacheEls();

      this.updateAgencyFromUrl();
      this.addAgencyAttribute();
      this.initializeMenu();
      this.updateLogo();
      this.updateHomepageHeading();
      this.updateSearchPlaceholder();
      this.hideContent();
    },

    cacheEls: function() {
      this.$mainLogoBox = $('.site-logo');
      this.$smallLogoBox = $('.site-logo-hq');
    },

    addAgencyAttribute: function() {
      $('html').attr('data-agency', this.agency);
    },

    initializeMenu: function() {
      var $menu = $('.header-menu');

      if(this.agencyData.blog_url) {
        $menu.find('.main-nav-blog a').attr('href', this.agencyData.blog_url);
      }

      $menu.addClass('loaded');
    },

    updateLogo: function() {
      var agency = window.App.tools.helpers.agency.get();
      var $logo = this.$mainLogoBox.find('img');
      var agencyData = window.App.tools.helpers.agency.getData();
      var isIntegrated = window.App.tools.helpers.agency.isIntegrated();
      var agencyImgSrc = $logo.attr('src').replace('moj_logo', 'moj_logo_' + agency);

      if(agency !== 'hq' && isIntegrated) {
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
      var $agencyLinkList = $('.agency-link-list');
      var $agencyAbbreviation = $('.agency-abbreviation');

      if($homepage.length) {
        $homeHeading.html(agencyData.label);

        $agencyLinkList.toggleClass('hidden', agencyData.url === '');
        $agencyLinkList.find('.agency').attr('data-department', this.agency);
        $agencyLinkList.find('a').attr('href', agencyData.url);
        $agencyLinkList.find('.label').html(agencyData.url_label || agencyData.label);
        $agencyAbbreviation.text(agencyData.abbreviation);
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
      var $menuItems = $('.header-menu .category-item:visible');
      var count = $menuItems.length;

      if(count > 0 && App.ie && App.ie <= 9) {
        $menuItems.css({
          width: '' + 100/count + '%'
        });
      }
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
