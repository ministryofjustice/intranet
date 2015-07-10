/** Accessibility
 */
(function($) {
  "use strict";

  var App = window.App;

  App.Accessibility = function() {
    this.init();
  };

  App.Accessibility.prototype = {
    init: function() {
      this.settings = {
        docExtensionsRegex: new RegExp('\.(doc|xls|xlsx|ppt|pps|pdf)$', 'i'),
        extensionElementClass: 'sr-only'
      };

      this.updateDocLinks();
    },

    cacheEls: function() {
    },

    bindEvents: function() {
    },

    updateDocLinks: function($container) {
      var _this = this;
      var $docLinks;
      var $link;

      if(!$container) {
        $container = $(window.document);
      }

      $docLinks = $container.find('a[href]').each(function() {
        if(_this.settings.docExtensionsRegex.test($(this).attr('href'))) {
          _this.updateDocLink($(this));
        }
      });
    },

    updateDocLink: function($link) {
      var $extension = $link.find('.' + this.settings.extensionElementClass);
      var extension = $link.attr('href').match(this.settings.docExtensionsRegex);
      extension = extension[0].substr(1).replace(/^\s*|\s*$/g, ''); //!!! Note: replace with trim() when we drop IE7 support

      if(!$extension.length) {
        $extension = $('<span>')
          .addClass(this.settings.extensionElementClass);

        $link.append($extension);
      }

      $extension.text(' ' + extension);
    }
  };
}(jQuery));
