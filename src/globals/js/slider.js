/* global jQuery */

;(function ($) {
  /**
 * Turns a set of elements into a slideshow
 *
 * Usage: Simply add your slideshow container element to script-loader.js and add .moji_slider() on to it.
 * In order for this to work you will need to enable one or both of the following options:
 *
 * @navigation {boolean} [default: false] true shows a 'back/next' navigation which can be styled with CSS.
 * @interval {integer} [default: 0] adding a value will cause the slideshow to autoplay using the set interval value in milliseconds (1000ms = 1s)
 */
  $.fn.moji_slider = function (navigation, interval) {
    var container = this
    var slide = container.find('.js-slide')
    var slideCount = slide.length
    var current = 1
    // Switches between slides
    var slider = function () {
      // TODO: This is a bit rudementary at the moment.
      // It might be nice in the future to add some animations to this
      slide.hide()
      slide.eq(current - 1).show()
    }

    // Switch between current slides, it will move forward unless specified otherwise
    var switchSlide = function (dir) {
      slider()
      if (dir === 'back') {
        current = (current > 1) ? current - 1 : slideCount
      } else {
        current = (current < slideCount) ? current + 1 : 1
      }
    }

    // Create the navigation and bind events to it
    var useNav = function () {
      slider() // activate the slider once
      var navContainer = $('<div class="js-nav-container" />')
      var backLink = $('<span class="js-nav-back">Back</span>')
      var nextLink = $('<span class="js-nav-next">Next</span>')
      navContainer.appendTo(container).append(backLink).append(nextLink)
      navContainer.on('click', '.js-nav-back', function () { switchSlide('back') })
      navContainer.on('click', '.js-nav-next', function () { switchSlide('next') })
    }
    // Check to see if the slider should be on a timer
    if (interval > 0) setInterval(switchSlide, interval)
    // Check to see if the slider should be manually navigated
    if (navigation === true) useNav()
    return container
  }
})(jQuery)
