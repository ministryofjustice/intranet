(function($) {
  "use strict";

  var App = window.App;
  var $imgThumbs = $('.thumbnail');
  var $videoThumbs = $('.video-thumbnail');
  var $lightboxImage = $('.lightbox-img');
  var $lightboxVideo = $('.lightbox-video');
  var $lbVideoContainer = $('.lightbox-video-container');
  var $lbImageContainer = $('.lightbox-image-container');
  var $lightboxImgCaption = $('.lightbox-img_caption');
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

      // Handles clicking on the image thumbnail
      if ($imgThumbs) {
        $imgThumbs.on('click', 'a', function(e) {
          e.preventDefault();
          var bigImage = $(this).attr('href');
          var bigImageCaption = $(this).find('img').attr('title');
          $lightboxImage.attr('src', bigImage);
          $lightboxImgCaption.find('h4').html(bigImageCaption);
          $lbImageContainer.attr('data-state','visible');
          $lbImageContainer.attr('title','visible');
        });
      }

      // Handles clicking on the video (Youtube) thumbnail
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

      // Hides lightbox when background is clicked on
      $lbImageContainer.on('click', function() {
        $lbImageContainer.attr('data-state', 'hidden');
        $lightboxImage.removeAttr('src');
      });

    }
  }; //App.LightBox.prototype function

}(jQuery));
