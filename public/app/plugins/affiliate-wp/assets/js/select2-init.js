/**
 * Select2 Initializations.
 *
 * @see includes/utils/trait-select2.php::init_select2() where you can
 *      automatically enqueue this script and set it up for select2 selectors.
 *
 * @since 2.12.0
 * @since 2.13.2 Updated with AJAX support.
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

		const args = select.data( 'args' );

		if (

			// We have args.
			'undefined' !== typeof args &&

			// If we have AJAX data, let's make sure and merge it first.
			args.hasOwnProperty( 'ajax' ) &&
				args.ajax.hasOwnProperty( 'data' )
		) {

			// Yes, we have to store it as plain data here, it will be a function below.
			const ajaxData = args.ajax.data;

			// Make sure params get sent to the AJAX request with the data in the args (merge).
			args.ajax.data = function( params ) {

				return jQuery.extend(
					params,
					ajaxData,
					{

						// Always use pagination by default.
						page: params.page || 1,
					}
				);
			};
		}

		window.jQuery( select ).select2( args );

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