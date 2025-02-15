<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

/**
 * Class PaymentGateway
 *
 * @package WPENON\Model
 */
abstract class PaymentGateway {
	protected static $gateway_name = '';

	private static $instances = array();

	protected $instance_id;

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
		
		add_filter( 'edd_payment_confirm_' . $gateway_name,  array( $this, 'confirmPage' ) );

		if ( ! empty( $this->listener_key ) ) {
			add_action( 'init', array( $this, '_listenForNotification' ) );
		}

		if ( ! $this->show_cc_form ) {
			add_action( 'edd_' . $gateway_name . '_cc_form', '__return_false' );
		}

		$this->instance_id = substr( md5( microtime() ), 0,5 );
	}

	public function _register( $gateways ) {
		$gateways[ static::$gateway_name ] = $this->gateway_args;

		return $gateways;
	}

	public function _listenForNotification() {
		// var_dump( 'DRIN' );

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
		$log_message = sprintf( 'Payment error for payment id #%s: %s', $payment_id, $message );
		$this->log( $log_message );

		do_action('wpenon_payment_error', $payment_id );

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

		$log_message = sprintf( 'Payment successful for payment id #%s with transaction id %s', $payment_id, $transaction_id );
		$this->log( $log_message );

		do_action('wpenon_payment_successful', $payment_id );

		edd_empty_cart();

		if ( $redirect_to ) {
			wp_redirect( $redirect_to );
			exit;
		} else {
			edd_send_to_success_page();
		}
	}

	protected function _handlePaymentProcessError( $payment_id, $message, $abort = false ) {
		$log_message = sprintf( 'Payment process error for payment id #%s: %s', $payment_id, $message ) . chr(13);
		$this->log( $log_message );

		edd_record_gateway_error( sprintf( __( '%s Notification Error', 'wpenon' ), $this->listener_key ), $message, $payment_id );

		if ( $abort ) {
			wp_send_json_error( array( 'message' => $message ), 400 );
		}
	}

	public function log( $message, $backtrace = false ) {
		if( $backtrace ) {
			ob_start();
			debug_print_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
			$trace = ob_get_contents();
			ob_end_clean();

			$message.= chr(13 ) . $trace;
		}

		$url = $_SERVER['REQUEST_URI'];
		$time = date('Y-m-d H:i:s' );
		$microtime = microtime();

		$line = $this->listener_key . ' Instance ID: #' . $this->instance_id . chr( 13 );
		$line.= $time . ' - ' . $microtime .  ' - ' . $url . chr(13) . $message . chr(13 );

		$file = fopen( WP_LOG_DIR . '/pamyents.log', 'a' );
		fputs( $file, $line  );
		fclose( $file );
	}

	protected function _handlePaymentProcessSuccess( $payment_id, $payment_status = null ) {
		if ( null !== $payment_status ) {
			edd_update_payment_status( $payment_id, $payment_status );
		}

		$log_message = sprintf( 'Payment Process Successful for payment id #%s with payment status %s', $payment_id, $payment_status );
		$this->log( $log_message );

		wp_send_json_success( array( 'message' => __( 'Payment status successfully updated.', 'wpenon' ) ) );
	}

	protected function _getSuccessURL( $payment_id ) {
		$url = add_query_arg( array(
			'payment-confirmation' => static::$gateway_name,
			'payment-id'           => $payment_id,
		), get_permalink( edd_get_option( 'success_page', false ) ) );

		$success_url = apply_filters( 'wpenon_payment_success_url', $url, $payment_id );

		return $success_url;
	}

	protected function _getFailureURL( $payment_id ) {
		$url = edd_get_failed_transaction_uri( '?payment-id=' . $payment_id );
		return apply_filters( 'wepenon_payment_failed_url', $url );
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

	public function confirmPage( $content ) {
		if ( ! isset( $_GET['payment-id'] ) && ! edd_get_purchase_session() ) {
			return $content;
		}
	
		edd_empty_cart();	

		ob_start();
		edd_get_template_part( 'payment', 'processing' );
		$content = ob_get_clean();
	
		return $content;	
	}
}
