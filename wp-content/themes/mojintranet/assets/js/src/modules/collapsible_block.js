/** Sticky news
 */
(function($) {
  "use strict";

  var App = window.App;

  App.CollapsibleBlock = function() {
    this.$toggleLinks = $('.collapsible-block-toggle');
    if(!this.$toggleLinks.length) { return; }
    this.init();
  };

  App.CollapsibleBlock.prototype = {
    init: function() {
      this.bindEvents();
    },

    bindEvents: function() {
      var _this = this;
      this.$toggleLinks.on('click', $.proxy(this.toggleList, this));
    },

    toggleList: function(e) {
      var $toggle = $(e.target);
      var $container = $toggle.closest('.collapsible-block-container');
      var openedLabel = $toggle.attr('data-opened-label');
      var closedLabel = $toggle.attr('data-closed-label');

      e.preventDefault();

      $container.toggleClass('opened');

      if($.type(openedLabel)!=='undefined' && $.type(closedLabel)!=='undefined') {
        $toggle.text($container.hasClass('opened') ? openedLabel : closedLabel);
      }
    }
  };
}(window.jQuery));
