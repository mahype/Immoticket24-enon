/**
 * Class Enon_Checkout
 *
 * @since 1.0.0
 */
class Enon_Checkout {
	/**
	 * Constructor.
	 *
	 * @param jQuery
	 *
	 * @since 1.0.0
	 */
	constructor( jQuery ) {
		this.jQuery = jQuery;
		this.add_event();
		console.log('Enon_Checkout');
	}

	/**
	 * Add trigger to button.
	 *
	 * @since 1.0.0
	 */
	add_event() {
		var self = this;

		self.jQuery( document ).on( 'click', '#edd-purchase-button', function( event ) {
			parent.postMessage(JSON.stringify({ 'set_to_top': true }), '*');
		});
	}
}

export default Enon_Checkout;
