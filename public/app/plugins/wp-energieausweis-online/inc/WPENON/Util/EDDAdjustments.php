<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class EDDAdjustments {
	private static $instance;

	public static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private $toggable_admin_pages = array();

	private $_temp_discount_id = 0;
	private $_temp_price = 100.0;
	private $_temp_cart_item_counter = 0;
	private $_significant_price_index = - 1;

	private function __construct() {
		$this->toggable_admin_pages = array( 'discounts', 'reports', 'tools', 'addons' );

		add_filter( 'edd_default_downloads_name', array( $this, '_postTypeName' ) );
		add_filter( 'edd_download_supports', array( $this, '_postTypeSupports' ) );
		add_filter( 'edd_download_post_type_args', array( $this, '_postTypeArgs' ) );

		remove_filter( 'the_content', 'edd_before_download_content' );
		remove_filter( 'the_content', 'edd_after_download_content' );

		add_action( 'admin_menu', array( $this, '_adjustAdminMenu' ), 11 );
		add_action( 'current_screen', array( $this, '_adjustAdminBehavior' ), 1, 1 );
		add_action( 'add_meta_boxes', array( $this, '_adjustDownloadMetaBoxes' ), 11 );
		add_filter( 'edd_metabox_fields_save', array( $this, '_adjustDownloadMetaBoxSave' ) );

		add_filter( 'edd_get_download_price', array( $this, '_adjustDownloadPrice' ), 10, 2 );
		add_filter( 'edd_has_variable_prices', array( $this, '_adjustHasVariablePrices' ), 10, 2 );
		add_filter( 'edd_get_variable_prices', array( $this, '_adjustVariablePrices' ), 10, 2 );
		add_filter( 'edd_variable_default_price_id', array( $this, '_adjustVariableDefaultPriceID' ), 10, 2 );

		add_filter( 'edd_can_purchase_download', array( $this, '_maybeDisallowDownloadPurchase' ), 10, 2 );

		add_action( 'wpenon_energieausweis_create', array( $this, '_setPriceDefaults' ), 10, 1 );

		add_filter( 'edd_settings_emails', array( $this, '_adjustEmailSettings' ) );
		add_filter( 'edd_get_option_email_logo', array( $this, '_adjustEmailLogoOption' ), 10, 3 );
		add_filter( 'edd_get_option_from_name', array( $this, '_adjustEmailFromNameOption' ), 10, 3 );
		add_filter( 'edd_get_option_from_email', array( $this, '_adjustEmailFromOption' ), 10, 3 );

		add_action( 'edd_insert_payment_args', array( $this, '_adjustPaymentArgs' ) );
		add_filter( 'edd_payment_number', array( $this, '_showPaymentTitleInsteadOfNumber' ), 10, 2 );

		add_filter( 'edd_payment_meta_user_info', array( $this, '_adjustPaymentMetaUserInfo' ) );

		add_filter( 'edd_download_files', array( $this, '_adjustDownloadFiles' ), 10, 3 );
		add_filter( 'edd_user_can_view_receipt_item', array( $this, '_adjustReceiptTable' ), 100, 2 );

		add_filter( 'shortcode_atts_purchase_link', array( $this, '_adjustButtons' ), 100, 1 );
		add_filter( 'edd_purchase_link_defaults', array( $this, '_adjustButtons' ), 100, 1 );
		add_filter( 'edd_checkout_button_purchase', array( $this, '_adjustPurchaseButton' ), 100, 1 );
		add_action( 'edd_checkout_cart_bottom', array( $this, '_printNewCertificateLink' ), 10, 0 );

		add_filter( 'edd_template_paths', array( $this, '_adjustTemplatePaths' ) );

		add_filter( 'edd_require_billing_address', '__return_true' );
		add_filter( 'edd_purchase_form_required_fields', array( $this, '_getRequiredUserInfoFields' ) );

		remove_action( 'edd_purchase_form_after_user_info', 'edd_user_info_fields' );
		add_action( 'edd_purchase_form_after_user_info', array( $this, '_displayUserInfoFields' ) );

		remove_action( 'edd_cc_form', 'edd_get_cc_form' );
		add_action( 'edd_cc_form', array( $this, '_displayCCFormFields' ) );

		remove_action( 'edd_after_cc_fields', 'edd_default_cc_address_fields' );
		add_action( 'edd_after_cc_fields', array( $this, '_displayCCAddressFields' ) );

		remove_action( 'edd_purchase_form_after_cc_form', 'edd_checkout_tax_fields', 999 );
		add_action( 'edd_purchase_form_after_cc_form', array( $this, '_forceCheckoutAddressFields' ), 999 );

		add_action( 'edd_payment_mode_after_gateways', array( $this, '_render_nonce_field' ) );

		add_action( 'edd_add_discount_form_before_type', array( $this, '_adjustDiscountTypeFieldBefore' ), 100 );
		add_action( 'edd_edit_discount_form_before_type', array( $this, '_adjustDiscountTypeFieldBefore' ), 100, 2 );
		add_action( 'edd_add_discount_form_before_amount', array( $this, '_adjustDiscountTypeFieldAfter' ), 1 );
		add_action( 'edd_edit_discount_form_before_amount', array( $this, '_adjustDiscountTypeFieldAfter' ), 1, 2 );
		add_action( 'edd_dcg_add_discount_form_top', array( $this, '_adjustBulkDiscountTypeFieldBefore' ), 100 );
		add_action( 'edd_dcg_add_discount_form_bottom', array( $this, '_adjustBulkDiscountTypeFieldAfter' ), 1 );
		add_filter( 'edd_discounted_amount', array( $this, '_adjustDiscountedAmount' ), 10, 1 );
		add_filter( 'edd_get_discount_amount', array( $this, 'setTempDiscountID' ), 10, 2 );
		add_filter( 'edd_cart_item_price', array( $this, '_setTempCartItemPrice' ), 10, 3 );

		add_action( 'edd_checkout_form_top', array( $this, '_addDiscountFixScript' ), 1 );

		add_action( 'widgets_init', array( $this, '_unregisterMemoryLimitErrorWidget' ), 20 );

		add_shortcode( 'edd_empty_cart', array( $this, 'empty_cart_shortcode' ) );

		// EDD Fixes (fees cannot include taxes in EDD Core)
		if ( function_exists( 'edd_prices_include_tax' ) && edd_prices_include_tax() ) {
			add_filter( 'edd_get_payment_subtotal', array( $this, 'eddfix_get_payment_subtotal' ), 10, 3 );
			add_filter( 'edd_get_cart_total', array( $this, 'eddfix_get_cart_total' ) );
			add_filter( 'edd_get_cart_fee_tax', array( $this, 'eddfix_get_cart_fee_tax' ) );

			remove_action( 'edd_gateway_paypal', 'edd_process_paypal_purchase' );
			add_action( 'edd_gateway_paypal', array( $this, 'eddfix_process_paypal_purchase' ), 5 );

			/* replace code in EDD_Payment->recalculate_total() with the following:
				$total = 0.0;
				foreach ( $this->cart_details as $item ) {
				  $total += $item['price'];
				}
				$total += $this->fees_total;
				$this->total = $total;*/
		}
	}

	public function eddfix_get_payment_subtotal( $subtotal, $payment_id, $payment ) {
		if ( $payment->fees ) {
			foreach ( $payment->fees as $fee ) {
				if ( ! empty( $fee['no_tax'] ) ) {
					$subtotal += $fee['amount'];
				} elseif ( floatval( $fee['amount'] ) > 0.0 ) {
					$subtotal += $fee['amount'] - edd_calculate_tax( $fee['amount'] );
				}
			}
		}

		return $subtotal;
	}

	public function eddfix_get_cart_total( $total ) {
		$fees_total    = (float) edd_get_cart_fee_total();
		$fees_subtotal = (float) $this->eddfix_get_cart_fee_subtotal();

		$total = $total - $fees_total + $fees_subtotal;

		return edd_sanitize_amount( $total );
	}

	public function eddfix_get_cart_fee_subtotal() {
		$total = (float) edd_get_cart_fee_total();
		$tax   = $this->eddfix_get_cart_fee_tax();

		return edd_sanitize_amount( $total - $tax );
	}

	public function eddfix_get_cart_fee_tax( $tax = 0 ) {
		$tax  = 0;
		$fees = edd_get_cart_fees();

		if ( $fees ) {
			foreach ( $fees as $fee_id => $fee ) {
				if ( ! empty( $fee['no_tax'] ) ) {
					continue;
				} elseif ( floatval( $fee['amount'] ) > 0.0 ) {
					$tax += edd_calculate_tax( $fee['amount'] );
				}
			}
		}

		return $tax;
	}

	public function empty_cart_shortcode() {
		edd_empty_cart();
	}

	public function eddfix_process_paypal_purchase( $purchase_data ) {
		if ( ! wp_verify_nonce( $purchase_data['gateway_nonce'], 'edd-gateway' ) ) {
			wp_die( __( 'Nonce verification has failed', 'easy-digital-downloads' ), __( 'Error', 'easy-digital-downloads' ), array( 'response' => 403 ) );
		}

		// Collect payment data
		$payment_data = array(
			'price'        => $purchase_data['price'],
			'date'         => $purchase_data['date'],
			'user_email'   => $purchase_data['user_email'],
			'purchase_key' => $purchase_data['purchase_key'],
			'currency'     => edd_get_currency(),
			'downloads'    => $purchase_data['downloads'],
			'user_info'    => $purchase_data['user_info'],
			'cart_details' => $purchase_data['cart_details'],
			'gateway'      => 'paypal',
			'status'       => ! empty( $purchase_data['buy_now'] ) ? 'private' : 'pending'
		);

		// Record the pending payment
		$payment = edd_insert_payment( $payment_data );

		// Check payment
		if ( ! $payment ) {
			// Record the error
			edd_record_gateway_error( __( 'Payment Error', 'easy-digital-downloads' ), sprintf( __( 'Payment creation failed before sending buyer to PayPal. Payment data: %s', 'easy-digital-downloads' ), json_encode( $payment_data ) ), $payment );
			// Problems? send back
			edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
		} else {
			// Only send to PayPal if the pending payment is created successfully
			$listener_url = add_query_arg( 'edd-listener', 'IPN', home_url( 'index.php' ) );

			$success_url = apply_filters( 'wpenon_payment_success_url', get_permalink( edd_get_option( 'success_page', false ) ), $payment );

			// Get the success url
			$return_url = add_query_arg( array(
				'payment-confirmation' => 'paypal',
				'payment-id'           => $payment
			), $success_url );

			// Get the PayPal redirect uri
			$paypal_redirect = trailingslashit( edd_get_paypal_redirect() ) . '?';

			// Setup PayPal arguments
			$paypal_args = array(
				'business'      => edd_get_option( 'paypal_email', false ),
				'email'         => $purchase_data['user_email'],
				'first_name'    => $purchase_data['user_info']['first_name'],
				'last_name'     => $purchase_data['user_info']['last_name'],
				'invoice'       => $purchase_data['purchase_key'],
				'no_shipping'   => '1',
				'shipping'      => '0',
				'no_note'       => '1',
				'currency_code' => edd_get_currency(),
				'charset'       => get_bloginfo( 'charset' ),
				'custom'        => $payment,
				'rm'            => '2',
				'return'        => $return_url,
				'cancel_return' => edd_get_checkout_uri(),
				'notify_url'    => $listener_url,
				'page_style'    => edd_get_paypal_page_style(),
				'cbt'           => get_bloginfo( 'name' ),
				'bn'            => 'EasyDigitalDownloads_SP'
			);

			if ( ! empty( $purchase_data['user_info']['address'] ) ) {
				$paypal_args['address1'] = $purchase_data['user_info']['address']['line1'];
				$paypal_args['address2'] = $purchase_data['user_info']['address']['line2'];
				$paypal_args['city']     = $purchase_data['user_info']['address']['city'];
				$paypal_args['country']  = $purchase_data['user_info']['address']['country'];
			}

			$paypal_extra_args = array(
				'cmd'    => '_cart',
				'upload' => '1'
			);

			$paypal_args = array_merge( $paypal_extra_args, $paypal_args );

			$check_amount = 0.0;

			// Add cart items
			$i = 1;
			if ( is_array( $purchase_data['cart_details'] ) && ! empty( $purchase_data['cart_details'] ) ) {
				foreach ( $purchase_data['cart_details'] as $item ) {

					$item_amount = round( ( $item['subtotal'] / $item['quantity'] ) - ( $item['discount'] / $item['quantity'] ), 2 );

					if ( $item_amount <= 0 ) {
						$item_amount = 0;
					}

					$paypal_args[ 'item_name_' . $i ] = stripslashes_deep( html_entity_decode( edd_get_cart_item_name( $item ), ENT_COMPAT, 'UTF-8' ) );
					$paypal_args[ 'quantity_' . $i ]  = $item['quantity'];
					$paypal_args[ 'amount_' . $i ]    = $item_amount;

					$check_amount += $paypal_args[ 'amount_' . $i ];

					if ( edd_use_skus() ) {
						$paypal_args[ 'item_number_' . $i ] = edd_get_download_sku( $item['id'] );
					}

					$i ++;

				}
			}

			// Add taxes to the cart
			if ( edd_use_taxes() ) {

				$tax_rate = \WPENON\Util\Format::int( ( ( $purchase_data['price'] / ( $purchase_data['price'] - edd_sanitize_amount( $purchase_data['tax'] ) ) ) - 1.0 ) * 100 );

				$paypal_args['tax_cart'] = edd_sanitize_amount( $purchase_data['tax'] );

				$check_amount += $paypal_args['tax_cart'];

			}

			// Calculate discount
			$discounted_amount = 0.00;
			if ( ! empty( $purchase_data['fees'] ) ) {
				$i = empty( $i ) ? 1 : $i;
				foreach ( $purchase_data['fees'] as $fee ) {
					if ( floatval( $fee['amount'] ) > '0' ) {
						// this is a positive fee
						$paypal_args[ 'item_name_' . $i ] = stripslashes_deep( html_entity_decode( wp_strip_all_tags( $fee['label'] ), ENT_COMPAT, 'UTF-8' ) );
						$paypal_args[ 'quantity_' . $i ]  = '1';
						$paypal_args[ 'amount_' . $i ]    = edd_sanitize_amount( floatval( $fee['amount'] ) / ( 1.0 + $tax_rate / 100.0 ) );

						$check_amount += $paypal_args[ 'amount_' . $i ];

						$i ++;
					} else {
						// This is a negative fee (discount)
						$discounted_amount += abs( $fee['amount'] );
					}
				}
			}

			if ( $discounted_amount > '0' ) {
				$paypal_args['discount_amount_cart'] = edd_sanitize_amount( $discounted_amount );

				$check_amount -= $paypal['discount_amount_cart'];
			}

			if ( $i > 1 ) {
				$inconsistency_fix = round( (float) $purchase_data['price'] - (float) $check_amount, 2 );
				if ( 0.0 != $inconsistency_fix ) {
					$paypal_args[ 'amount_' . ( $i - 1 ) ] += $inconsistency_fix;
				}
			}

			$paypal_args = apply_filters( 'edd_paypal_redirect_args', $paypal_args, $purchase_data );

			// Build query
			$paypal_redirect .= http_build_query( $paypal_args );

			// Fix for some sites that encode the entities
			$paypal_redirect = str_replace( '&amp;', '&', $paypal_redirect );

			// Redirect to PayPal
			wp_redirect( $paypal_redirect );
			exit;
		}

	}

	public function _postTypeName( $defaults ) {
		return array(
			'singular' => __( 'Energieausweis', 'wpenon' ),
			'plural'   => __( 'Energieausweise', 'wpenon' ),
		);
	}

	public function _postTypeSupports( $defaults ) {
		return array( 'title', 'thumbnail' );
	}

	public function _postTypeArgs( $defaults ) {
		$defaults['menu_position'] = WPENON_MENU_POSITION;

		return $defaults;
	}

	public function _adjustAdminMenu() {
		foreach ( $this->toggable_admin_pages as $admin_page ) {
			if ( defined( 'WPENON_' . strtoupper( $admin_page ) ) && ! constant( 'WPENON_' . strtoupper( $admin_page ) ) ) {
				remove_submenu_page( 'edit.php?post_type=download', 'edd-' . $admin_page );
			}
		}
	}

	public function _adjustAdminBehavior( $screen ) {
		foreach ( $this->toggable_admin_pages as $admin_page ) {
			if ( $screen->base == 'download_page_edd-' . $admin_page && defined( 'WPENON_' . strtoupper( $admin_page ) ) && ! constant( 'WPENON_' . strtoupper( $admin_page ) ) ) {
				wp_redirect( admin_url( 'edit.php?post_type=download' ) );
				exit;
			}
		}

		remove_action( 'wp_dashboard_setup', 'edd_register_dashboard_widgets', 10 );
		if ( defined( 'WPENON_REPORTS' ) && WPENON_REPORTS ) {
			add_action( 'wp_dashboard_setup', array( $this, '_registerDashboardWidgets' ), 10 );
		}
	}

	public function _adjustDownloadMetaBoxes() {
		$post_types = apply_filters( 'edd_download_metabox_post_types', array( 'download' ) );
		foreach ( $post_types as $post_type ) {
			remove_meta_box( 'edd_product_files', $post_type, 'normal' );
			remove_meta_box( 'edd_product_notes', $post_type, 'normal' );
			if ( defined( 'WPENON_REPORTS' ) && ! WPENON_REPORTS ) {
				remove_meta_box( 'edd_product_stats', $post_type, 'side' );
			}
		}
	}

	public function _adjustDownloadMetaBoxSave( $fields ) {
		if ( isset( $fields['edd_download_files'] ) ) {
			unset( $fields['edd_download_files'] );
		}
		if ( isset( $fields['_edd_bundled_products'] ) ) {
			unset( $fields['_edd_bundled_products'] );
		}

		return $fields;
	}

	public function _registerDashboardWidgets() {
		if ( current_user_can( apply_filters( 'edd_dashboard_stats_cap', 'view_shop_reports' ) ) ) {
			wp_add_dashboard_widget( 'edd_dashboard_sales', __( 'Energieausweis Statistiken', 'wpenon' ), 'edd_dashboard_sales_widget' );
		}
	}

	public function _adjustDownloadPrice( $price, $id = 0 ) {
		if ( ! $id || get_post_status( $id ) == 'auto-draft' ) {
			$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis( $id );

			return $this->_getPriceDefaults( $energieausweis->type, 'edd_price' );
		}

		return $price;
	}

	public function _adjustHasVariablePrices( $ret, $id = 0 ) {
		if ( ! $id || get_post_status( $id ) == 'auto-draft' ) {
			$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis( $id );

			return $this->_getPriceDefaults( $energieausweis->type, '_variable_pricing' );
		}

		return $ret;
	}

	public function _adjustVariablePrices( $prices, $id = 0 ) {
		if ( ! $id || get_post_status( $id ) == 'auto-draft' ) {
			$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis( $id );

			return $this->_getPriceDefaults( $energieausweis->type, 'edd_variable_prices' );
		}

		return $prices;
	}

	public function _adjustVariableDefaultPriceID( $price_id, $id ) {
		if ( ! $id || get_post_status( $id ) == 'auto-draft' ) {
			$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis( $id );

			return $this->_getPriceDefaults( $energieausweis->type, '_edd_default_price_id' );
		}

		return $price_id;
	}

	public function _maybeDisallowDownloadPurchase( $can_purchase, $download ) {
		if ( $can_purchase ) {
			$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis( $download->ID );
			if ( ! $energieausweis || ! $energieausweis->isFinalized() ) {
				return false;
			}
		}

		return $can_purchase;
	}

	public function _setPriceDefaults( $energieausweis ) {
		$id       = $energieausweis->ID;
		$defaults = $this->_getPriceDefaults( $energieausweis->type );
		foreach ( $defaults as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}
	}

	public function _getPriceDefaults( $type, $mode = '' ) {
		$defaults                        = array();
		$price                           = wpenon_get_option( $type . '_download_price' );
		$defaults['edd_price']           = edd_sanitize_amount( $price );
		$defaults['_variable_pricing']   = ( WPENON_POSTAL || WPENON_AUDIT ) ? true : false;
		$defaults['edd_variable_prices'] = array(
			array(
				'name'   => __( 'Download', 'wpenon' ),
				'amount' => wpenon_get_option( $type . '_download_price' ),
			),
		);
		if ( WPENON_POSTAL ) {
			$defaults['edd_variable_prices'][] = array(
				'name'   => $this->getPostalDefaultName(),
				'amount' => $this->getPostalDefaultAmount( $type ),
			);
		}
		if ( WPENON_AUDIT ) {
			$defaults['edd_variable_prices'][] = array(
				'name'   => $this->getAuditDefaultName(),
				'amount' => $this->getAuditDefaultAmount( $type ),
			);
		}
		if ( WPENON_POSTAL && WPENON_AUDIT ) {
			$defaults['edd_variable_prices'][] = array(
				'name'   => $this->getPostalAuditDefaultName(),
				'amount' => $this->getPostalAuditDefaultAmount( $type ),
			);
		}
		$defaults['_edd_default_price_id'] = 0;

		if ( ! empty( $mode ) ) {
			if ( isset( $defaults[ $mode ] ) ) {
				return $defaults[ $mode ];
			}

			return false;
		}

		return $defaults;
	}

	public function getPostalDefaultName() {
		if ( defined( 'WPENON_POSTAL_NAME' ) && WPENON_POSTAL_NAME ) {
			return WPENON_POSTAL_NAME;
		}

		return __( 'Sendung per Post', 'wpenon' );
	}

	public function getPostalDefaultAmount( $type ) {
		if ( defined( 'WPENON_POSTAL_ADDITIONAL_AMOUNT' ) && WPENON_POSTAL_ADDITIONAL_AMOUNT ) {
			return floatval( wpenon_get_option( $type . '_download_price' ) ) + WPENON_POSTAL_ADDITIONAL_AMOUNT;
		}

		return wpenon_get_option( $type . '_postal_price' );
	}

	public function getAuditDefaultName() {
		if ( defined( 'WPENON_AUDIT_NAME' ) && WPENON_AUDIT_NAME ) {
			return WPENON_AUDIT_NAME;
		}

		return __( 'Kontrolle', 'wpenon' );
	}

	public function getAuditDefaultAmount( $type ) {
		if ( defined( 'WPENON_AUDIT_ADDITIONAL_AMOUNT' ) && WPENON_AUDIT_ADDITIONAL_AMOUNT ) {
			return floatval( wpenon_get_option( $type . '_download_price' ) ) + WPENON_AUDIT_ADDITIONAL_AMOUNT;
		}

		return wpenon_get_option( $type . '_audit_price' );
	}

	public function getPostalAuditDefaultName() {
		return $this->getPostalDefaultName() . ' & ' . $this->getAuditDefaultName();
	}

	public function getPostalAuditDefaultAmount( $type ) {
		if ( defined( 'WPENON_POSTAL_ADDITIONAL_AMOUNT' ) && WPENON_POSTAL_ADDITIONAL_AMOUNT && defined( 'WPENON_AUDIT_ADDITIONAL_AMOUNT' ) && WPENON_AUDIT_ADDITIONAL_AMOUNT ) {
			return floatval( wpenon_get_option( $type . '_download_price' ) ) + WPENON_POSTAL_ADDITIONAL_AMOUNT + WPENON_AUDIT_ADDITIONAL_AMOUNT;
		} elseif ( defined( 'WPENON_POSTAL_ADDITIONAL_AMOUNT' ) && WPENON_POSTAL_ADDITIONAL_AMOUNT ) {
			return floatval( wpenon_get_option( $type . '_audit_price' ) ) + WPENON_POSTAL_ADDITIONAL_AMOUNT;
		} elseif ( defined( 'WPENON_AUDIT_ADDITIONAL_AMOUNT' ) && WPENON_AUDIT_ADDITIONAL_AMOUNT ) {
			return floatval( wpenon_get_option( $type . '_postal_price' ) ) + WPENON_AUDIT_ADDITIONAL_AMOUNT;
		}

		return wpenon_get_option( $type . '_postal_audit_price' );
	}

	public function _adjustEmailSettings( $settings ) {
		if ( isset( $settings['email_logo'] ) ) {
			unset( $settings['email_logo'] );
		}
		if ( isset( $settings['from_name'] ) ) {
			unset( $settings['from_name'] );
		}
		if ( isset( $settings['from_email'] ) ) {
			unset( $settings['from_email'] );
		}

		return $settings;
	}

	public function _adjustEmailLogoOption( $value, $key = 'email_logo', $default = '' ) {
		$settings = \WPENON\Util\Settings::instance();

		return $settings->firmenlogo;
	}

	public function _adjustEmailFromNameOption( $value, $key = 'from_name', $default = '' ) {
		$settings = \WPENON\Util\Settings::instance();

		return $settings->firmenname;
	}

	public function _adjustEmailFromOption( $value, $key = 'from_email', $default = '' ) {
		$settings = \WPENON\Util\Settings::instance();

		return $settings->automail;
	}

	public function _adjustPaymentArgs( $args, $payment_data = array() ) {
		$args['post_title'] = self::_generatePaymentTitle();

		return $args;
	}

	public function _showPaymentTitleInsteadOfNumber( $number, $payment_id ) {
		if ( ! edd_get_option( 'enable_sequential' ) && is_numeric( $number ) ) {
			$number = get_the_title( $payment_id );
		}

		return $number;
	}

	public function _adjustPaymentMetaUserInfo( $user_info ) {
		$customer = EDD()->customers->get_customer_by( 'email', $user_info['email'] );
		if ( $customer && isset( $customer->id ) ) {
			$customer_meta = \WPENON\Util\CustomerMeta::instance()->getCustomerMeta( $customer->id );

			if ( ! isset( $user_info['address'] ) ) {
				$user_info['address'] = array();
			}

			$address_fields = array( 'line1', 'line2', 'zip', 'city', 'state', 'country' );
			foreach ( $address_fields as $address_field ) {
				if ( isset( $customer_meta[ $address_field ] ) ) {
					if ( ! isset( $user_info['address'][ $address_field ] ) ) {
						$user_info['address'][ $address_field ] = $customer_meta[ $address_field ];
					}
					unset( $customer_meta[ $address_field ] );
				}
			}

			$user_info = array_merge( $user_info, $customer_meta );
		}

		return $user_info;
	}

	public function _adjustDownloadFiles( $files, $post_id, $variable_price_id = 0 ) {
		return array();
	}

	public function _adjustReceiptTable( $show, $item ) {
		global $edd_receipt_args;

		if ( ! empty( $item['in_bundle'] ) || ! $show ) {
			return $show;
		}

		$item_url = \WPENON\Model\EnergieausweisManager::getVerifiedPermalink( $item['id'] );

		$payment_id = $edd_receipt_args['id'];

		$price_id = edd_get_cart_item_price_id( $item );
		?>

		<tr>
			<td>
				<div class="edd_purchase_receipt_product_name">
					<?php echo esc_html( $item['name'] ); ?>
					<?php if ( ! is_null( $price_id ) ) : ?>
						<span
							class="edd_purchase_receipt_price_name">&nbsp;&ndash;&nbsp;<?php echo edd_get_price_option_name( $item['id'], $price_id, $payment_id ); ?></span>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $item_url ) ) : ?>
					<div class="wpenon_purchase_receipt_link">
						<p style="font-size: 12px;"><a
								href="<?php echo esc_url( $item_url ); ?>"><?php _e( '(Zurück zum Energieausweis)', 'wpenon' ); ?></a>
						</p>
					</div>
				<?php endif; ?>

				<?php if ( $edd_receipt_args['notes'] ) : ?>
					<div
						class="edd_purchase_receipt_product_notes"><?php echo wpautop( edd_get_product_notes( $item['id'] ) ); ?></div>
				<?php endif; ?>
			</td>
			<?php if ( edd_use_skus() ) : ?>
				<td><?php echo edd_get_download_sku( $item['id'] ); ?></td>
			<?php endif; ?>
			<?php if ( edd_item_quantities_enabled() ) { ?>
				<td><?php echo $item['quantity']; ?></td>
			<?php } ?>
			<td>
				<?php if ( empty( $item['in_bundle'] ) ) : // Only show price when product is not part of a bundle ?>
					<?php echo edd_currency_filter( edd_format_amount( $item['price'] ) ); ?>
				<?php endif; ?>
			</td>
		</tr>

		<?php
		return false;
	}

	public function _adjustButtons( $args ) {
		if ( isset( $args['class'] ) ) {
			$args['class'] = str_replace( 'edd-submit', 'btn', $args['class'] );
		}

		return $args;
	}

	public function _adjustPurchaseButton( $output ) {
		return str_replace( 'class="edd-submit ', 'class="btn ', $output );
	}

	public function _printNewCertificateLink() {
		$settings = \WPENON\Util\Settings::instance();

		$bw_url = get_permalink( $settings->new_bw_page );
		$vw_url = get_permalink( $settings->new_vw_page );

		wp_enon_log( sprintf( 'Showing checkout page with Session data %s', $_SESSION['edd']['edd_cart']) );

		?>
		<p class="text-center">
			<?php printf( __( 'Möchten Sie noch einen <a href="%1$s">Bedarfsausweis</a> oder <a href="%2$s">Verbrauchsausweis</a> erstellen?', 'wpenon' ), esc_url( $bw_url ), esc_url( $vw_url ) ); ?>
		</p>
		<?php
	}

	public function _adjustTemplatePaths( $template_paths ) {
		$template_paths[70] = WPENON_DATA_PATH . '/templates/edd';
		$template_paths[80] = WPENON_PATH . '/templates/edd';

		return $template_paths;
	}

	public function _getRequiredUserInfoFields( $required_fields ) {
		$new_required_fields = array(
			'edd_last'     => array(
				'error_id'      => 'invalid_last_name',
				'error_message' => __( 'Bitte geben Sie Ihren Nachnamen ein', 'wpenon' ),
			),
			'card_address' => array(
				'error_id'      => 'invalid_last_name',
				'error_message' => __( 'Bitte geben Sie Ihre Straße und Hausnummer ein', 'wpenon' ),
			),
		);

		return array_merge( $required_fields, $new_required_fields );
	}

	public function _displayUserInfoFields() {
		$energieausweis = null;

		$cart_items = edd_get_cart_contents();
		if ( $cart_items ) {
			foreach ( $cart_items as $key => $item ) {
				$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis( $item['id'] );
				break;
			}
		}

		$owner_data = array();
		if ( $energieausweis !== null && WP_DEBUG ) {
		//   	$owner_data = $energieausweis->getOwnerData();
		}

		$fields = array(
			'edd_email'            => __( 'Email Address', 'easy-digital-downloads' ),
			'edd_first'            => __( 'First Name', 'easy-digital-downloads' ),
			'edd_last'             => __( 'Last Name', 'easy-digital-downloads' ),
			'wpenon_business_name' => __( 'Firmenname', 'wpenon' ),
			'wpenon_ustid'         => __( 'USt-Identifikationsnummer', 'wpenon' ),
			'wpenon_telefon'       => __( 'Telefonnummer', 'wpenon' ),
		);

		$enable_placeholders = apply_filters( 'wpenon_enable_purchase_placeholders', true );

		?>
		<fieldset id="edd_checkout_user_info">
			<span><legend><?php echo apply_filters( 'edd_checkout_personal_info_text', __( 'Personal Info', 'easy-digital-downloads' ) ); ?></legend></span>
			<?php foreach ( $fields as $field_slug => $field_title ) : ?>
				<?php
				$field_id        = str_replace( '_', '-', $field_slug );
				$field_shortslug = str_replace( array( 'edd_', 'wpenon_' ), '', $field_slug );
				$field_value     = isset( $owner_data[ $field_shortslug ] ) ? $owner_data[ $field_shortslug ] : '';

				if ( 'email' == $field_shortslug ) {
					do_action( 'edd_purchase_form_before_email' );
				}
				?>
				<p id="<?php echo $field_id; ?>-wrap">
					<label class="edd-label" for="<?php echo $field_id; ?>">
						<?php echo $field_title; ?>
						<?php if ( edd_field_is_required( $field_slug ) ) : ?>
							<span class="edd-required-indicator">*</span>
						<?php else : ?>
							<?php _e( '(optional)', 'wpenon' ); ?>
						<?php endif; ?>
					</label>
					<input class="edd-input<?php if ( edd_field_is_required( $field_slug ) ) {
						echo ' required';
					} ?>" type="text" name="<?php echo $field_slug; ?>"
					       id="<?php echo $field_id; ?>"<?php echo $enable_placeholders ? ' placeholder="' . $field_title . '"' : ''; ?>
					       value="<?php echo $field_value; ?>"<?php echo 'email' == $field_shortslug && ! empty( $field_value ) ? ' readonly' : ''; ?>>
				</p>
				<?php
				if ( 'email' == $field_shortslug ) {
					do_action( 'edd_purchase_form_after_email' );
				}
				?>
			<?php endforeach; ?>
			<?php do_action( 'edd_purchase_form_user_info' ); ?>
			<?php do_action( 'edd_purchase_form_user_info_fields' ); ?>
		</fieldset>
		<?php
	}

	public function _displayCCFormFields() {
		$enable_placeholders = apply_filters( 'wpenon_enable_purchase_placeholders', true );

		ob_start(); ?>

		<?php do_action( 'edd_before_cc_fields' ); ?>

		<fieldset id="edd_cc_fields" class="edd-do-validate">
			<span><legend><?php _e( 'Credit Card Info', 'easy-digital-downloads' ); ?></legend></span>
			<?php if ( is_ssl() ) : ?>
				<div id="edd_secure_site_wrapper">
					<span class="padlock"></span>
					<span><?php _e( 'This is a secure SSL encrypted payment.', 'easy-digital-downloads' ); ?></span>
				</div>
			<?php endif; ?>
			<p id="edd-card-number-wrap">
				<label for="card_number" class="edd-label">
					<?php _e( 'Card Number', 'easy-digital-downloads' ); ?>
					<span class="edd-required-indicator">*</span>
					<span class="card-type"></span>
				</label>
				<span
					class="edd-description"><?php _e( 'The (typically) 16 digits on the front of your credit card.', 'easy-digital-downloads' ); ?></span>
				<input type="text" autocomplete="off" name="card_number" id="card_number"
				       class="card-number edd-input required"<?php echo $enable_placeholders ? ' placeholder="' . __( 'Card number', 'easy-digital-downloads' ) . '"' : ''; ?> />
			</p>
			<p id="edd-card-cvc-wrap">
				<label for="card_cvc" class="edd-label">
					<?php _e( 'CVC', 'easy-digital-downloads' ); ?>
					<span class="edd-required-indicator">*</span>
				</label>
				<span
					class="edd-description"><?php _e( 'The 3 digit (back) or 4 digit (front) value on your card.', 'easy-digital-downloads' ); ?></span>
				<input type="text" size="4" maxlength="4" autocomplete="off" name="card_cvc" id="card_cvc"
				       class="card-cvc edd-input required"<?php echo $enable_placeholders ? ' placeholder="' . __( 'Security code', 'easy-digital-downloads' ) . '"' : ''; ?> />
			</p>
			<p id="edd-card-name-wrap">
				<label for="card_name" class="edd-label">
					<?php _e( 'Name on the Card', 'easy-digital-downloads' ); ?>
					<span class="edd-required-indicator">*</span>
				</label>
				<span
					class="edd-description"><?php _e( 'The name printed on the front of your credit card.', 'easy-digital-downloads' ); ?></span>
				<input type="text" autocomplete="off" name="card_name" id="card_name"
				       class="card-name edd-input required"<?php echo $enable_placeholders ? ' placeholder="' . __( 'Card name', 'easy-digital-downloads' ) . '"' : ''; ?> />
			</p>
			<?php do_action( 'edd_before_cc_expiration' ); ?>
			<p class="card-expiration">
				<label for="card_exp_month" class="edd-label">
					<?php _e( 'Expiration (MM/YY)', 'easy-digital-downloads' ); ?>
					<span class="edd-required-indicator">*</span>
				</label>
				<span
					class="edd-description"><?php _e( 'The date your credit card expires, typically on the front of the card.', 'easy-digital-downloads' ); ?></span>
				<select id="card_exp_month" name="card_exp_month"
				        class="card-expiry-month edd-select edd-select-small required">
					<?php for ( $i = 1; $i <= 12; $i ++ ) {
						echo '<option value="' . $i . '">' . sprintf( '%02d', $i ) . '</option>';
					} ?>
				</select>
				<span class="exp-divider"> / </span>
				<select id="card_exp_year" name="card_exp_year"
				        class="card-expiry-year edd-select edd-select-small required">
					<?php for ( $i = date( 'Y' ); $i <= date( 'Y' ) + 10; $i ++ ) {
						echo '<option value="' . $i . '">' . substr( $i, 2 ) . '</option>';
					} ?>
				</select>
			</p>
			<?php do_action( 'edd_after_cc_expiration' ); ?>

		</fieldset>
		<?php
		do_action( 'edd_after_cc_fields' );

		echo ob_get_clean();
	}

	public function _displayCCAddressFields() {
		$enable_placeholders = apply_filters( 'wpenon_enable_purchase_placeholders', true );

		$field_labels = array(
			'card_address'    => __( 'Billing Address', 'easy-digital-downloads' ),
			'card_address_2'  => __( 'Billing Address Line 2 (optional)', 'easy-digital-downloads' ),
			'card_city'       => __( 'Billing City', 'easy-digital-downloads' ),
			'card_zip'        => __( 'Billing Zip / Postal Code', 'easy-digital-downloads' ),
			'billing_country' => __( 'Billing Country', 'easy-digital-downloads' ),
			'card_state'      => __( 'Billing State / Province', 'easy-digital-downloads' ),
		);

		$field_labels = apply_filters( 'wpenon_address_field_labels', $field_labels );

		ob_start(); ?>
		<fieldset id="edd_cc_address" class="cc-address">
			<span><legend><?php _e( 'Billing Details', 'easy-digital-downloads' ); ?></legend></span>
			<?php do_action( 'edd_cc_billing_top' ); ?>
			<p id="edd-card-address-wrap">
				<label for="card_address" class="edd-label">
					<?php echo $field_labels['card_address']; ?>
					<?php if ( edd_field_is_required( 'card_address' ) ) { ?>
						<span class="edd-required-indicator">*</span>
					<?php } ?>
				</label>
				<span
					class="edd-description"><?php _e( 'The primary billing address for your credit card.', 'easy-digital-downloads' ); ?></span>
				<input type="text" id="card_address" name="card_address"
				       class="card-address edd-input<?php if ( edd_field_is_required( 'card_address' ) ) {
					       echo ' required';
				       } ?>"<?php echo $enable_placeholders ? ' placeholder="' . __( 'Address line 1', 'easy-digital-downloads' ) . '"' : ''; ?> />
			</p>
			<p id="edd-card-address-2-wrap">
				<label for="card_address_2" class="edd-label">
					<?php echo $field_labels['card_address_2']; ?>
					<?php if ( edd_field_is_required( 'card_address_2' ) ) { ?>
						<span class="edd-required-indicator">*</span>
					<?php } ?>
				</label>
				<span
					class="edd-description"><?php _e( 'The suite, apt no, PO box, etc, associated with your billing address.', 'easy-digital-downloads' ); ?></span>
				<input type="text" id="card_address_2" name="card_address_2"
				       class="card-address-2 edd-input<?php if ( edd_field_is_required( 'card_address_2' ) ) {
					       echo ' required';
				       } ?>"<?php echo $enable_placeholders ? ' placeholder="' . __( 'Address line 2', 'easy-digital-downloads' ) . '"' : ''; ?> />
			</p>
			<p id="edd-card-zip-wrap">
				<label for="card_zip" class="edd-label">
					<?php echo $field_labels['card_zip']; ?>
					<?php if ( edd_field_is_required( 'card_zip' ) ) { ?>
						<span class="edd-required-indicator">*</span>
					<?php } ?>
				</label>
				<span
					class="edd-description"><?php _e( 'The zip or postal code for your billing address.', 'easy-digital-downloads' ); ?></span>
				<input type="text" size="4" name="card_zip"
				       class="card-zip edd-input<?php if ( edd_field_is_required( 'card_zip' ) ) {
					       echo ' required';
				       } ?>"<?php echo $enable_placeholders ? ' placeholder="' . __( 'Zip / Postal code', 'easy-digital-downloads' ) . '"' : ''; ?> />
			</p>
			<p id="edd-card-city-wrap">
				<label for="card_city" class="edd-label">
					<?php echo $field_labels['card_city']; ?>
					<?php if ( edd_field_is_required( 'card_city' ) ) { ?>
						<span class="edd-required-indicator">*</span>
					<?php } ?>
				</label>
				<span class="edd-description"><?php _e( 'The city for your billing address.', 'easy-digital-downloads' ); ?></span>
				<input type="text" id="card_city" name="card_city"
				       class="card-city edd-input<?php if ( edd_field_is_required( 'card_city' ) ) {
					       echo ' required';
				       } ?>"<?php echo $enable_placeholders ? ' placeholder="' . __( 'City', 'easy-digital-downloads' ) . '"' : ''; ?> />
			</p>
			<p id="edd-card-country-wrap">
				<label for="billing_country" class="edd-label">
					<?php echo $field_labels['billing_country']; ?>
					<?php if ( edd_field_is_required( 'billing_country' ) ) { ?>
						<span class="edd-required-indicator">*</span>
					<?php } ?>
				</label>
				<span
					class="edd-description"><?php _e( 'The country for your billing address.', 'easy-digital-downloads' ); ?></span>
				<select name="billing_country" id="billing_country" data-nonce="<?php echo wp_create_nonce( 'edd-country-field-nonce' ); ?>"
				        class="billing_country edd-select<?php if ( edd_field_is_required( 'billing_country' ) ) {
					        echo ' required';
				        } ?>">
					<?php
					$selected_country = edd_get_shop_country();
					$countries = edd_get_country_list();
					foreach ( $countries as $country_code => $country ) {
						echo '<option value="' . esc_attr( $country_code ) . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
					}
					?>
				</select>
			</p>
			<p id="edd-card-state-wrap">
				<label for="card_state" class="edd-label">
					<?php echo $field_labels['card_state']; ?>
					<?php if ( edd_field_is_required( 'card_state' ) ) { ?>
						<span class="edd-required-indicator">*</span>
					<?php } ?>
				</label>
				<span
					class="edd-description"><?php _e( 'The state or province for your billing address.', 'easy-digital-downloads' ); ?></span>
				<?php
				$states = edd_get_shop_states( $selected_country );
				if ( ! empty( $states ) ) : ?>
					<select name="card_state" id="card_state"
					        class="card_state edd-select<?php if ( edd_field_is_required( 'card_state' ) ) {
						        echo ' required';
					        } ?>">
						<?php
						foreach ( $states as $state_code => $state ) {
							echo '<option value="' . $state_code . '">' . $state . '</option>';
						}
						?>
					</select>
				<?php else : ?>
					<input type="text" size="6" name="card_state" id="card_state"
					       class="card_state edd-input"<?php echo $enable_placeholders ? ' placeholder="' . __( 'State / Province', 'easy-digital-downloads' ) . '"' : ''; ?> />
				<?php endif; ?>
			</p>
			<?php do_action( 'edd_cc_billing_bottom' ); ?>
			<?php //  wp_nonce_field( 'edd-checkout-address-fields', 'edd-checkout-address-fields-nonce', false, true ); ?>
		</fieldset>
		<?php
		echo ob_get_clean();
	}

	public function _render_nonce_field() {
		wp_nonce_field( 'edd-checkout-address-fields', 'edd-checkout-address-fields-nonce', false, true );
	}

	public function _forceCheckoutAddressFields() {
		if ( ! did_action( 'edd_after_cc_fields', 'edd_default_cc_address_fields' ) ) {
			$this->_displayCCAddressFields();
		}
	}

	public function _adjustDiscountTypeFieldBefore( $discount_id = 0, $discount = null ) {
		ob_start();
	}

	public function _adjustDiscountTypeFieldAfter( $discount_id = 0, $discount = null ) {
		$output = ob_get_clean();

		$types = array(
			'percent'         => __( 'Percentage', 'easy-digital-downloads' ),
			'flat'            => __( 'Flat amount', 'easy-digital-downloads' ),
			'most_expensive'  => __( 'Prozentsatz, nur auf den teuersten Artikel angewendet', 'wpenon' ),
			'least_expensive' => __( 'Prozentsatz, nur auf den günstigsten Artikel angewendet', 'wpenon' ),
		);

		$discount_type = $discount_id ? edd_get_discount_type( $discount_id ) : '';

		?>
		<tr>
			<th scope="row" valign="top">
				<label for="edd-type"><?php _e( 'Type', 'easy-digital-downloads' ); ?></label>
			</th>
			<td>
				<select name="type" id="edd-type">
					<?php foreach ( $types as $value => $label ) : ?>
						<option
							value="<?php echo $value; ?>" <?php selected( $discount_type, $value ); ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php _e( 'The kind of discount to apply for this discount.', 'easy-digital-downloads' ); ?></p>
			</td>
		</tr>
		<?php
	}

	public function _adjustBulkDiscountTypeFieldBefore() {
		ob_start();
	}

	public function _adjustBulkDiscountTypeFieldAfter() {
		$output = ob_get_clean();

		$options_html = '<option value="percent">' . __( 'Prozentsatz', 'wpenon' ) . '</option>';
		$options_html .= '<option value="flat">' . __( 'Pauschale', 'wpenon' ) . '</option>';
		$options_html .= '<option value="most_expensive">' . __( 'Prozentsatz, nur auf den teuersten Artikel angewendet', 'wpenon' ) . '</option>';
		$options_html .= '<option value="least_expensive">' . __( 'Prozentsatz, nur auf den günstigsten Artikel angewendet', 'wpenon' ) . '</option>';

		$output = preg_replace( '/<select name="type" id="edd-type">(.+)<\/select>/Us', '<select name="type" id="edd-type">' . $options_html . '</select>', $output );

		echo $output;
	}

	public function _adjustDiscountedAmount( $amount ) {
		$discount_id = $this->_temp_discount_id;

		if ( $discount_id ) {
			$discount_type = edd_get_discount_type( $discount_id );
			if ( in_array( $discount_type, array( 'least_expensive', 'most_expensive' ) ) ) {
				$cart_items = edd_get_cart_contents();
				if ( $this->_temp_cart_item_counter == count( $cart_items ) ) {
					$this->_temp_cart_item_counter = 0;
				}
				if ( 0 == $this->_temp_cart_item_counter ) {
					$excluded_products              = edd_get_discount_excluded_products( $discount_id );
					$significant_price              = false;
					$this->_significant_price_index = - 1;
					foreach ( $cart_items as $i => $cart_item ) {
						if ( ! in_array( $cart_item['id'], $excluded_products ) ) {
							$cart_item_price = edd_get_cart_item_price( $cart_item['id'], $cart_item['options'] );
							if ( false === $significant_price || 'most_expensive' == $discount_type && $cart_item_price > $significant_price || 'least_expensive' == $discount_type && $cart_item_price < $significant_price ) {
								$significant_price              = $cart_item_price;
								$this->_significant_price_index = $i;
							}
						}
					}
				}

				if ( $this->_temp_cart_item_counter != $this->_significant_price_index ) {
					$this->_temp_cart_item_counter ++;

					return $this->_temp_price;
				}
				$this->_temp_cart_item_counter ++;
			}
		}

		return $amount;
	}

	public function setTempDiscountID( $amount, $code_id ) {
		$this->_temp_discount_id = $code_id;

		return $amount;
	}

	public function _setTempCartItemPrice( $price, $download_id, $options ) {
		$this->_temp_price = $price;

		return $price;
	}

	public function _addDiscountFixScript() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				var origCheckValidity = undefined;

				$(document.body).on('edd_discount_applied', function (e, discount) {
					if ('0.00' == discount.total_plain) {
						$('#wpenon_sepa_fields').slideUp();
						$('#edd_cc_address').slideDown();
						$('#edd_payment_mode_select').slideUp();

						var purchase_form = document.getElementById('edd_purchase_form');
						origCheckValidity = purchase_form.checkValidity;
						purchase_form.checkValidity = undefined;
					}
				});

				$(document.body).on('edd_discount_removed', function (e, discount) {
					if ('0.00' != discount.total_plain) {
						$('#wpenon_sepa_fields').slideDown();
						$('#edd_cc_fields').slideDown();
						$('#edd_payment_mode_select').slideDown();

						var purchase_form = document.getElementById('edd_purchase_form');
						purchase_form.checkValidity = origCheckValidity;
						origCheckValidity = undefined;
					}
				});
			});
		</script>
		<?php
	}

	public function _unregisterMemoryLimitErrorWidget() {
		unregister_widget( 'edd_product_details_widget' );
	}

	public static function _generatePaymentTitle( $id = null, $after_publish = false ) {
		return \WPENON\Util\Format::generateTitle( WPENON_RECHNUNG_TITLE_STRUCTURE, 'edd_payment', $id, $after_publish );
	}
}
