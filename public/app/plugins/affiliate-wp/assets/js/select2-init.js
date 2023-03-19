/**
 * Select2 Initializations.
 *
 * @see includes/utils/trait-select2.php::init_select2() where you can
 *      automatically enqueue this script and set it up for select2 selectors.
 *
 * @since 2.12.0
 */

( function () {

	if (
		! window.hasOwnProperty( 'jQuery' ) ||
		! window.hasOwnProperty( 'affwpSelect2' )
	) {
		return; // We need these to be enqueued and localized by the trait.
	}

	const element = window.jQuery( window.affwpSelect2.selector );

	if ( ! element.length ) {
		return; // Fail gracefully, there may not be groups to select (no <select>).
	}

	window.jQuery( window.affwpSelect2.selector )
		.select2( window.affwpSelect2.args );

	if ( ! window.affwpSelect2.hasOwnProperty( 'labelSelector' ) || window.affwpSelect2.labelSelector.length <= 0 ) {
		return;
	}

	const label = window.jQuery( window.affwpSelect2.labelSelector );

	if ( ! label.length ) {
		return; // Fail gracefully, there may not be groups to select (no <select>).
	}

	// Focus on the element (select) when the label clicked.
	label.on( 'click', function() {
		element.select2( 'open' );
	} );

} ) ();