/* global $ */

// Quickly knocked this together for POC.

var container = $('.js-need-to-know')

var moveSlide = function () {
  container.find('.js-slide').each(function () {
    // If the slide has current-slide on it then remove current and add it to the next slide
    if ($(this).hasClass('current-slide')) {
      $(this).removeClass('current-slide').next().addClass('current-slide')
    }
  })
}

$(document).ready(function () {
  setInterval(moveSlide, 1000)
})
