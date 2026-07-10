jQuery(
	function (jQuery) {
		jQuery( document ).ready(
			function () {
				// Require post title when adding/editing Project Summaries
				jQuery( 'body' ).on(
					'submit.edit-post',
					'#post',
					function () {
						// If the title isn't set
						if (jQuery( '#title' ).val().replace( / /g, '' ).length === 0) {
							// Show the alert
							if ( ! jQuery( '#title-required' ).length) {
								jQuery( '#titlewrap' )
								.append( '<div id="title-required"><em>Title is required.</em></div>' )
								.css(
									{
										'padding': '5px',
										'margin': '5px 0',
										'background': '#ffebe8',
										'border': '1px solid #c00'
									}
								)
							}
							// Hide the spinner
							jQuery( '#major-publishing-actions .spinner' ).hide()
							// The buttons get "disabled" added to them on submit. Remove that class.
							jQuery( '#major-publishing-actions' ).find( ':button, :submit, a.submitdelete, #post-preview' ).removeClass( 'disabled' )
							// Focus on the title field.
							jQuery( '#title' ).focus()
							return false
						}
					}
				)
			}
		)
	}
)
