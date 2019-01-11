<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

abstract class PaymentGateway {
	protected static $gateway_name = '';

	private static $instances = array();

	public static function instance() {
		if ( ! isset( self::$instances[ static::$gateway_name ] ) ) {
			self::$instances[ static::$gateway_name ] = new static();
		}

		return self::$instances[ static::$gateway_name ];
	}

	protected $gateway_args = array();
	protected $show_cc_form = true;
	protected $listener_key = '';

	protected function __construct() {
		add_filter( 'edd_payment_gateways', array( $this, '_register' ) );

		$gateway_name = static::$gateway_name;

		add_action( 'edd_gateway_' . $gateway_name, array( $this, 'verifyNonce' ), 1 );
		add_action( 'edd_gateway_' . $gateway_name, array( $this, 'processPurchase' ) );

		if ( ! empty( $this->listener_key ) ) {
			add_action( 'init', array( $this, '_listenForNotification' ) );
		}

		if ( ! $this->show_cc_form ) {
			add_action( 'edd_' . $gateway_name . '_cc_form', '__return_false' );
		}
	}

	public function _register( $gateways ) {
		$gateways[ static::$gateway_name ] = $this->gateway_args;

		return $gateways;
	}

	public function _listenForNotification() {
		if ( isset( $_GET['edd-listener'] ) && $_GET['edd-listener'] == $this->listener_key ) {
			$this->processPurchaseNotification( $_GET );
		}
	}

	public function verifyNonce( $purchase_data ) {
		if ( ! wp_verify_nonce( $purchase_data['gateway_nonce'], 'edd-gateway' ) ) {
			wp_die( __( 'Nonce verification has failed', 'easy-digital-downloads' ), __( 'Error', 'easy-digital-downloads' ), array( 'response' => 403 ) );
		}
	}

	public abstract function processPurchase( $purchase_data );

	public abstract function processPurchaseNotification( $data );

	protected function _makePaymentData( $purchase_data, $additional_data = array() ) {
		$payment_data = array(
			'price'        => $purchase_data['price'],
			'date'         => $purchase_data['date'],
			'user_email'   => $purchase_data['user_email'],
			'purchase_key' => $purchase_data['purchase_key'],
			'currency'     => edd_get_currency(),
			'downloads'    => $purchase_data['downloads'],
			'user_info'    => $purchase_data['user_info'],
			'cart_details' => $purchase_data['cart_details'],
			'gateway'      => static::$gateway_name,
			'status'       => 'pending'
		);

		return array_merge( $payment_data, $additional_data );
	}

	protected function _handlePaymentError( $payment_id, $message, $sendback = false ) {
		edd_record_gateway_error( __( 'Payment Error', 'wpenon' ), $message, $payment_id );
		if ( $payment_id ) {
			edd_update_payment_status( $payment_id, 'failed' );
		}
		if ( $sendback ) {
			edd_send_back_to_checkout( '?payment-mode=' . static::$gateway_name );
			exit;
		}
	}

	protected function _handlePaymentSuccess( $payment_id, $transaction_id = null, $redirect_to = null ) {
		if ( $transaction_id ) {
			edd_set_payment_transaction_id( $payment_id, $transaction_id );
		}

		edd_empty_cart();

		if ( $redirect_to ) {
			wp_redirect( $redirect_to );
			exit;
		} else {
			edd_send_to_success_page();
		}
	}

	protected function _handlePaymentProcessError( $payment_id, $message, $abort = false ) {
		edd_record_gateway_error( sprintf( __( '%s Notification Error', 'wpenon' ), $this->listener_key ), $message, $payment_id );

		$logfile_name = dirname( ABSPATH ) . '/pamyent-errors-9868765456.log';
		$file = fopen( $logfile_name, 'a' );
		fwrite( $file, sprintf( '%s Payment Error for Payment %s: %s', $this->listener_key, $payment_id, $message ) );
		fclose( $file );

		if ( $abort ) {
			wp_send_json_error( array( 'message' => $message ), 400 );
		}
	}

	protected function _handlePaymentProcessSuccess( $payment_id, $payment_status = null ) {
		if ( null !== $payment_status ) {
			edd_update_payment_status( $payment_id, $payment_status );
		}

		wp_send_json_success( array( 'message' => __( 'Payment status successfully updated.', 'wpenon' ) ) );
	}

	protected function _getSuccessURL( $payment_id ) {
		return add_query_arg( array(
			'payment-confirmation' => static::$gateway_name,
			'payment-id'           => $payment_id,
		), get_permalink( edd_get_option( 'success_page', false ) ) );
	}

	protected function _getFailureURL( $payment_id ) {
		return edd_get_failed_transaction_uri( '?payment-id=' . $payment_id );
	}

	protected function _getListenerURL() {
		if ( ! empty( $this->listener_key ) ) {
			return home_url( '/edd-listener/' . $this->listener_key . '/' );
		}

		return false;
	}

	protected function _getPaymentIDByTransactionID( $transaction_id ) {
		global $wpdb;

		if ( ! empty( $transaction_id ) ) {
			$payment_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_edd_payment_transaction_id' AND meta_value = %s LIMIT 1", $transaction_id ) );
			if ( $payment_id ) {
				$payment_id = absint( $payment_id );
				if ( edd_get_payment_gateway( $payment_id ) == static::$gateway_name ) {
					return $payment_id;
				}
			}
		}

		return 0;
	}
}
