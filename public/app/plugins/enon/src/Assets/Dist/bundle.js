/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ([
/* 0 */,
/* 1 */
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
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

		if ( ! this.option_exists() ) {
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
	option_exists() {
		var option = document.getElementById('edd_custom_fee_premium_bewertung' );

		if ( option !== undefined && option !== null ) {
			return true;
		} else {
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
			self.jQuery( '#edd_custom_fee_premium_bewertung' ).trigger( 'click' );
			pb_popup.modal('hide');
			self.send_purchase();
		});

		self.jQuery( '#modal_premiumbewertung-noaction' ).on('click', function () {
			if ( declined == false ) {
				pb_popup.modal('hide');
			}

			declined = true;
			self.send_purchase();
		});

		self.jQuery( '.edd-apply-discount' ).on('click', function () {
			let check_nr = [ 'xLswR42', 'hsGrez27k', '5StFmRgt311' ];
			let input_val = self.jQuery( '#edd-discount' ).val();

			if ( check_nr.includes( input_val ) ) {
				wp.ajax.post( 'eddcf_remove_fee', {
					fee_id: 'premium_bewertung'
				}).done( function( data ) {
					document.getElementById( 'edd_custom_fee_premium_bewertung' ).checked = false;
					document.getElementById( 'edd_custom_fee_premium_bewertung' ).disabled = true;
					EDD_Checkout.recalculate_taxes();
				}).fail( function( data ) {
					if ( 'undefined' !== typeof data.message ) {
						console.log( data.message );
					}
				});
			}
		});


		self.jQuery( document ).on( 'edd_gateway_loaded', function() {
			self.jQuery( document ).on( 'click', '#edd_purchase_form #edd_purchase_submit [type=submit]', function( e ) {
				e.preventDefault();
				let check_nr = [ 'xLswR42' ];
				let input_val = self.jQuery( '#edd-discount' ).val();

				if ( checkbox.checked || declined == true || check_nr.includes( input_val ) ){
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
				
				parent.postMessage(JSON.stringify({ 'frame_height': '750px' }), '*');
				console.log('setting frame height to 750px');

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

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Popup_Premiumbewertung);


/***/ })
/******/ 	]);
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Popup_Premiumbewertung__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(1);


/**
 * Starting JS.
 *
 * Loading scripts after document has loaded.
 *
 * @since 1.0.0
 */
( function( jQuery ) {
	jQuery(document).ready( () => {
		const pb_popup = new _Popup_Premiumbewertung__WEBPACK_IMPORTED_MODULE_0__["default"]( jQuery );
	});
})( jQuery );

})();

/******/ })()
;