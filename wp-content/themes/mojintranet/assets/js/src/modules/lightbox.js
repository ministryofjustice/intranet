(function($) {
  "use strict";

  var App = window.App;
  var $imgThumbs = $('.thumbnail');
  var $videoThumbs = $('.video-thumbnail');
  var $lightboxImage = $('.lightbox-img');
  var $lightboxVideo = $('.lightbox-video');
  var $lbVideoContainer = $('.lightbox-video-container');
  var $lbImageContainer = $('.lightbox-image-container');
  var $btnClose = $('.btn-close');

  App.LightBox = function() {
    this.$top = $('.thumbnail, .video-thumbnail');
    if (!this.$top.length) {
      return;
    }
    this.init();
  };

  App.LightBox.prototype = {
    init: function() {

      if ($imgThumbs) {
        $imgThumbs.on('click', 'a', function(e) {
          e.preventDefault();
          $lbImageContainer.attr('data-state', 'visible');
          var bigImage = $(this).attr('href');
          $lightboxImage.attr('src', bigImage);

          // On closing the image lightbox
          $btnClose.on('click', function() {
            $lbImageContainer.attr('data-state', 'hidden');
            $lightboxImage.removeAttr('src');
          });
        });
      }

      if ($videoThumbs) {
        $videoThumbs.on('click', 'a', function(e) {
          e.preventDefault();

          $lbVideoContainer.attr('data-state', 'visible');
          var bigVideo = $(this).attr('href');
          $lightboxVideo.append(bigVideo);
        });
      }

      $lbVideoContainer.on('click', function() {
        $lbVideoContainer.attr('data-state', 'hidden');
        $lightboxVideo.empty();
        $lightboxVideo.html(""); //IE7 fix
      });


      $lbImageContainer.on('click', function() {
        $lbImageContainer.attr('data-state', 'hidden');
        $lightboxImage.removeAttr('src');
      });

    }
  }; //App.LightBox.prototype function

}(jQuery));
