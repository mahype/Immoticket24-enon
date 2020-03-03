jQuery( document ).ready( function( $ ) {

	var processing = false;

	function process_fee( id, mode ) {
		var action = 'eddcf_add_fee';
		if ( 'remove' === mode ) {
			action = 'eddcf_remove_fee';
		}

		processing = true;

		wp.ajax.post( action, {
			fee_id: id
		}).done( function( data ) {
			EDD_Checkout.recalculate_taxes();
			processing = false;
		}).fail( function( data ) {
			if ( 'undefined' !== typeof data.message ) {
				console.log( data.message );
			}
			processing = false;
		});
	}

	function add_fee( id ) {
		process_fee( id, 'add' );
	}

	function remove_fee( id ) {
		process_fee( id, 'remove' );
	}

	$( '.eddcf-custom-fee' ).on( 'change', function( e ) {
		if ( processing ) {
			e.preventDefault();
		}

		var $this = $( this );
		if ( $this.is( ':checked' ) ) {
			add_fee( $this.val() );
		} else {
			remove_fee( $this.val() );
		}
	});

});
