/* global jQuery */

;(function ($) {
 /**
 * Ensures that in a set of elements, they all have an equal height (equal to the height of the largest elemement)
 *
 * Usage: Simply add your container element to script-loader.js and add .moji_equaliser() on to it.
 * Make sure you reference the container and child elements. e.g. $('.c-news-list > .js-article-item').moji_equaliser()
 *
 */
  $.fn.moji_equaliser = function () {
    var container = this
    var tallestHeight = 0
    var heightCheck = 1
    for (var i = 0; i < container.length; i++) {
      var height = $(container[i]).outerHeight(true)
      if (height > tallestHeight) tallestHeight = height
      if (heightCheck === container.length) {
        // All items accounted for, now make all items the same height
        $(container).css('height', tallestHeight + 'px')
      } else {
        heightCheck++
      }
    }

    return container
  }
})(jQuery)
