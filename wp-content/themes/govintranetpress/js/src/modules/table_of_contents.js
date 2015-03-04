/** Table of contents
 * Note: it's designed to work with only one instance per page
 */
(function($) {
  "use strict";

  var App = window.App;

  App.TableOfContents = function() {
    this.$tableOfContents = $('.table-of-contents');
    this.isImported = !!$('.guidance-and-support-content[data-is-imported="1"]').length;
    if(!this.$tableOfContents.length || this.isImported) { return; }
    this.init();
  };

  App.TableOfContents.prototype = {
    init: function() {
      this.cacheEls();
      this.bindEvents();
      this.generate();
      this.initialized = true;
    },

    cacheEls: function() {
      this.$tableOfContentsBox = $('.table-of-contents-box');
      this.$contentContainer = $(this.$tableOfContents.attr('data-content-selector'));
    },

    bindEvents: function() {
    },

    generate: function() {
      var _this = this;

      if(!this.initialized) { return; }

      this.$tableOfContents.empty();
      //find all H2 tags with ID's
      this.$contentContainer.find('h2').each(function() {
        var $el = $(this);
        var $item = $('<li><a></a></li>');
        var attr;

        if(!$el.filter('[id]').length) {
          attr = $el.text().toLowerCase();
          attr = attr.replace(/[^A-Za-z0-9\s-]/g, '');
          attr = attr.replace(/[\s+]/g, '-');
          $el.attr('id', attr);
        }

        $item.find('a')
          .text($el.text())
          .attr('href', '#'+$el.attr('id'));
        $item.appendTo(_this.$tableOfContents);
      });

      this.updateBoxVisibility();
    },

    updateBoxVisibility: function() {
      this.$tableOfContentsBox.toggleClass('visible', this.$tableOfContents.find('> li').length > 0);
    }
  };
}(jQuery));
