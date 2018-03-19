/* global jQuery */

; (function ($) {
  $.fn.moji_feedbackForm = function () {
    var form = $('.js-reveal-target')
    var trigger = $('.js-reveal-trigger')
    trigger.click(function (e) {
      e.preventDefault()
      form.toggle()
    })
  }
})(jQuery)
