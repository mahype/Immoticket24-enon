/**
 * Select2 Initializations.
 *
 * @see includes/utils/trait-select2.php::init_select2() where you can
 *      automatically enqueue this script and set it up for select2 selectors.
 *
 * @since 2.12.0
 */

/* globals jQuery */

( function () {

	if (
		! window.hasOwnProperty( 'jQuery' )
	) {
		return; // We need these to be enqueued and localized by the trait.
	}

	const element = window.jQuery( 'select.select2' );

	if ( ! element.length ) {
		return; // Fail gracefully, there may not be groups to select (no <select>).
	}

	jQuery( element ).each( function( index, el ) {

		const select = jQuery( el );

		window.jQuery( select ).select2( select.data( 'args' ) );

		const label = window.jQuery( 'label[for="' + select.data( 'label' ) + '"]' );

		if ( ! label.length ) {
			return; // Fail gracefully, there may not be groups to select (no <select>).
		}

		const triggerElement = jQuery( '#' + select.attr( 'id' ) );

		if ( ! triggerElement.length ) {
			return;
		}

		// Focus on the select (select) when the label clicked.
		label.on( 'click', function() {
			jQuery( triggerElement ).select2( 'open' );
		} );
	} );
} ) ();