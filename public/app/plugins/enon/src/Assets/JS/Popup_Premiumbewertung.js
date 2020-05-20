/**
 * Class Popup Premiumbewertung.
 *
 * @since 1.0.0
 */
class Popup_Premiumbewertung {
	/**
	 * Constructor.
	 *
	 * @param jQuery
	 *
	 * @since 1.0.0
	 */
	constructor( jQuery ) {
		this.jQuery = jQuery;

		if ( ! this.modal_exists() ) {
			return;
		}

		this.remove_edd_event();
		this.add_event();
	}

	/**
	 * Check if modal exists.
	 *
	 * @returns {boolean}
	 *
	 * @since 1.0.0
	 */
	modal_exists() {
		var modal = document.getElementById('modal_premiumbewertung' );

		if ( typeof( modal ) != 'undefined' && modal != null){
			return true;
		} else{
			return false;
		}
	}

	/**
	 * Take edd event from list.
	 *
	 * @returns {*}
	 *
	 * @since 1.0.0
	 */
	remove_edd_event() {
		var self = this;

		let event_list = self.jQuery._data( document, 'events' ).click;
		let trigger_index = self.get_edd_listener_index( event_list );

		// Take event
		let edd_event = event_list[ trigger_index ];

		// Remove event
		self.jQuery( document ).off( 'click', '#edd_purchase_form #edd_purchase_submit [type=submit]' );
		self.jQuery( document ).off( 'click', '#edd_purchase_form #edd_login_fields input[type=submit]' );

		return edd_event;
	}

	/**
	 * Add trigger to button.
	 *
	 * @param jQuery
	 *
	 * @since 1.0.0
	 */
	add_event() {
		var self = this;
		var pb_popup = self.jQuery( '#modal_premiumbewertung' );
		var declined = false;
		var checkbox = document.getElementById( 'edd_custom_fee_premium_bewertung' );

		self.jQuery( '#modal_premiumbewertung' ).modal({
			show: false,
			closeExisting: false
		});

		self.jQuery( '#modal_premiumbewertung-action' ).on('click', function () {
			checkbox.checked = true;

			let phone_label = document.querySelector('label[for="wpenon-telefon"]');
			phone_label.innerHTML = phone_label.innerHTML.replace('(optional)', '<span class="edd-required-indicator">*</span>');
			pb_popup.modal('hide');
		});

		self.jQuery( '#modal_premiumbewertung-noaction' ).on('click', function () {
			if ( declined == false ) {
				pb_popup.modal('hide');
			}

			declined = true;
			self.send_purchase();
		});

		self.jQuery( document ).on( 'edd_gateway_loaded', function() {
			self.jQuery( document ).on( 'click', '#edd_purchase_form #edd_purchase_submit [type=submit]', function( e ) {
				e.preventDefault();

				if ( checkbox.checked || declined == true ){
					self.send_purchase();
				} else {
					pb_popup.modal('show');
				}
			} );
		});
	}

	/**
	 * Get index of edd listener.
	 *
	 * @param event_list
	 * @returns {boolean}
	 *
	 * @since 1.0.0
	 */
	get_edd_listener_index( event_list ) {
		let found_index = false;

		event_list.forEach( function( element, index  ) {
				if ( '#edd_purchase_form #edd_login_fields input[type=submit]' == element.selector ) {
					found_index = index;
				}
			});

		return found_index;
	}

	/**
	 * Sending purchase
	 *
	 * @param button
	 *
	 * @since 1.0.0
	 */
	send_purchase() {
		var self = this;

		var eddPurchaseform = document.getElementById('edd_purchase_form');

		if( typeof eddPurchaseform.checkValidity === "function" && false === eddPurchaseform.checkValidity() ) {
			return;
		}

		var button = self.jQuery('#edd-purchase-button');
		var complete_purchase_val = button.val();

		self.jQuery(button).val(edd_global_vars.purchase_loading);
		self.jQuery(button).prop( 'disabled', true );
		self.jQuery(button).after('<span class="edd-loading-ajax edd-loading"></span>');

		self.jQuery.post(edd_global_vars.ajaxurl, self.jQuery('#edd_purchase_form').serialize() + '&action=edd_process_checkout&edd_ajax=true', function(data) {
			if ( self.jQuery.trim(data) == 'success' ) {
				self.jQuery('.edd_errors').remove();
				self.jQuery('.edd-error').hide();
				self.jQuery(eddPurchaseform).submit();
			} else {
				self.jQuery('#edd-purchase-button').val(complete_purchase_val);
				self.jQuery('.edd-loading-ajax').remove();
				self.jQuery('.edd_errors').remove();
				self.jQuery('.edd-error').hide();
				self.jQuery( edd_global_vars.checkout_error_anchor ).before(data);
				self.jQuery('#edd-purchase-button').prop( 'disabled', false );

				self.jQuery(document.body).trigger( 'edd_checkout_error', [ data ] );
			}
		});
	}
}

export default Popup_Premiumbewertung;
