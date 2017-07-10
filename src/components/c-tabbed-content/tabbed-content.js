/* global jQuery */

/* tabbedContent function */
;(function ($) {
  $.fn.moji_tabbedContent = function () {
    // create the navigation for the tabs
    var panels = $('.js-tabbed-content')
    var tabNav = $('<ul class="c-tabbed-content__nav" />').insertBefore('.js-tabbed-content:first-of-type')
    panels.filter(':first-of-type').show().addClass('u-current')
    $('.js-tabbed-content').each(function () {
      var title = $(this).data('tab-title')
      // Add the tabs
      $('<li />').text(title.replace('-', ' ')).appendTo(tabNav).click(function () {
        // Hide/show the panels
        panels.removeClass('u-current')
        $(this).parent().find('li').removeClass('u-current')
        $(this).addClass('u-current')
        var thisTab = $(this).text().replace(' ', '-')
        panels.filter('[data-tab-title="' + thisTab + '"]').addClass('u-current')
      }).filter(':first-of-type').addClass('u-current')
    })
  }
})(jQuery)
