(function($) {
  // Redefines initCrop so we can specify our own aspect ratio (3:2)
  imageEdit.initCrop = function(postid, image, parent) {
		var t = this,
			selW = $('#imgedit-sel-width-' + postid),
			selH = $('#imgedit-sel-height-' + postid),
			$img;

		t.iasapi = $(image).imgAreaSelect({
			parent: parent,
			instance: true,
			handles: true,
			keys: true,
			minWidth: 3,
			minHeight: 3,

			onInit: function( img ) {
				// Ensure that the imgareaselect wrapper elements are position:absolute
				// (even if we're in a position:fixed modal)
				$img = $( img );
				$img.next().css( 'position', 'absolute' )
					.nextAll( '.imgareaselect-outer' ).css( 'position', 'absolute' );

				parent.children().mousedown(function(e){
					var ratio = false, sel, defRatio;

					if ( e.shiftKey ) {
						sel = t.iasapi.getSelection();
						defRatio = t.getSelRatio(postid);
						ratio = ( sel && sel.width && sel.height ) ? sel.width + ':' + sel.height : defRatio;
					}

					t.iasapi.setOptions({
						aspectRatio: "3:2"
					});
				});
			},

			onSelectStart: function() {
				imageEdit.setDisabled($('#imgedit-crop-sel-' + postid), 1);
			},

			onSelectEnd: function(img, c) {
				imageEdit.setCropSelection(postid, c);
			},

			onSelectChange: function(img, c) {
				var sizer = imageEdit.hold.sizer;
				selW.val( imageEdit.round(c.width / sizer) );
				selH.val( imageEdit.round(c.height / sizer) );
			}
		});
	}

})(jQuery);
