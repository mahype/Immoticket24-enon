import Enon_Checkout from './Enon_Checkout';
import Popup_Premiumbewertung from './Popup_Premiumbewertung';

/**
 * Starting JS.
 *
 * Loading scripts after document has loaded.
 *
 * @since 1.0.0
 */
( function( jQuery ) {
	jQuery(document).ready( () => {
		const pb_popup = new Popup_Premiumbewertung( jQuery );
		const checkout = new Enon_Checkout( jQuery );
	});
})( jQuery );
