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
      this.bindEvents();

      this.update();
      this.hideContent();
    },

    cacheEls: function() {
    },

    bindEvents: function() {
    },

    update: function() {
      var $homepage = $('.template-home');
      var agency = window.App.tools.helpers.agency.getData(this.agency);

      $('html').attr('data-agency', this.agency);

      if($homepage.length) {
        $('.template-home h1').html(agency.label);

        if (agency.url === '') {
          $('.agency-link-list').addClass('hidden');

          $('.agency-link-list a')
            .attr('href', '')
            .find('.label').html('');
        }
        else {
          $('.agency-link-list').removeClass('hidden');

          $('.agency-link-list a')
            .attr('href', agency.url)
            .find('.label').html(agency.label);
        }
      }
    },

    hideContent: function() {
      var selectorsToHide = this.settings.hideForAgency[this.agency] || [];

      $.each(selectorsToHide, function(index, selector) {
        $(selector).addClass('agency-hidden');
      });
    }
  };
}(window.jQuery));
