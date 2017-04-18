/* global $ */

// Quickly knocked this together for POC.

var activateJS = function () {
  $('body').removeClass('no-js').addClass('js')
}

$(document).ready(function () {
  activateJS()
})
