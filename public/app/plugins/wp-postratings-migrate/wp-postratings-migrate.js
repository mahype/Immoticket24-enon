/* global ajaxurl, console */

(function( $, wp ) {

	$( document ).ready( function() {
		var $button      = $( '#postratings-migrate-submit' );
		var $target      = $( '#postratings-migrate-target' );
		var $targetValue = $( '#postratings-migrate-target-value' );
		var $copy        = $( '#postratings-migrate-copy' );

		var postId   = $target.data( 'post-id' );
		var postType = $target.data( 'post-type' );
		var nonce    = $target.data( 'nonce' );

		$target.autocomplete({
			source:    ajaxurl + '?action=postratings_autocomplete&exclude_post_id=' + postId + '&post_type=' + postType + '&nonce=' + nonce,
			delay:     500,
			minLength: 3,
			position: {
				my: 'left top',
				at: 'left bottom',
				collision: 'fit'
			},
			open: function() {
				$( this ).addClass( 'open' );
			},
			close: function() {
				$( this ).removeClass( 'open' );
			},
			select: function( e, ui ) {
				e.preventDefault();

				$target.val( ui.item.label );
				$targetValue.val( ui.item.value ).trigger( 'change' );
			}
		});

		$targetValue.on( 'change', function() {
			if ( $target.val() && $target.val().length ) {
				$button.prop( 'disabled', false );
			} else {
				$button.prop( 'disabled', true );
			}
		});

		$button.on( 'click', function( e ) {
			e.preventDefault();

			var data = {
				nonce: nonce,
				source_id: postId,
				target_id: $targetValue.val(),
			};

			if ( $copy.prop( 'checked' ) ) {
				data.copy = '1';
			}

			wp.ajax.post( 'postratings_migrate', data ).done( function( message ) {
				renderMessage( message );
			}).fail( function( message ) {
				renderMessage( message, true );
			});
		});

		function renderMessage( message, error ) {
			var classes = error ? 'notice notice-error' : 'notice notice-success';

			var $message = $( '<div class="' + classes + '"></div>' );

			$message.html( '<p>' + message + '</p>' ).insertAfter( $button );

			setTimeout( function() {
				$message.fadeOut( 'slow' );
			}, 1000 );
		}
	});

})( jQuery, wp );
