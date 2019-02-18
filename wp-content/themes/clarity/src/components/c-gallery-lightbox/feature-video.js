;
(function ($) {
  $.fn.moji_featureVideo = function () {
    $('.popup-youtube').magnificPopup({
      type: 'iframe'
    })
    $('.popup-image').magnificPopup({
      type: 'image'
    })

    function obama () {
      console.log(obama)
    }
    obama()
  }
})(jQuery)
