<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

class PaymentGatewaySofortueberweisung extends \WPENON\Model\PaymentGateway {
	protected static $gateway_name = 'sofortueberweisung';

	protected function __construct() {
		$this->gateway_args = array(
			'admin_label'    => __( 'Sofortüberweisung', 'wpenon' ),
			'checkout_label' => __( 'Sofortüberweisung', 'wpenon' ),
		);

		add_filter( 'edd_settings_gateways', array( $this, '_registerSettings' ) );
		add_filter( 'edd_accepted_payment_icons', array( $this, '_registerIcon' ) );

		$this->show_cc_form = false;

		$this->listener_key = 'SOFORT';

		parent::__construct();
	}

	protected function get_config_key() {
		if ( edd_is_test_mode() ) {
			return  '64225:573466:3c410e9ca42d0a4ef88f8abaab89ad18';
		}

		return  edd_get_option( 'sofortueberweisung_config_key', '' );
	}

	public function processPurchase( $purchase_data ) {
		$payment_data = $this->_makePaymentData( $purchase_data );

		// Record the pending payment
		$payment_id = edd_insert_payment( $payment_data );

		if ( ! $payment_id ) {
			$this->_handlePaymentError( $payment_id, sprintf( __( 'Payment creation failed while processing a Sofortueberweisung purchase. Payment data: %s', 'wpenon' ), json_encode( $payment_data ) ), true );
		}

		$config_key = $this->get_config_key();
		if ( substr_count( $config_key, ':' ) != 2 ) {
			$this->_handlePaymentError( $payment_id, __( 'Sofortueberweisung purchase could not be processed because of invalid or missing config key.', 'wpenon' ), true );
		}

		$sofortueberweisung = new \Sofort\SofortLib\Sofortueberweisung( $config_key );

		$sofortueberweisung->setReason( \WPENON\Util\PaymentMeta::instance()->getDepositKey( $payment_id ) );
		$sofortueberweisung->setAmount( $purchase_data['price'] );
		$sofortueberweisung->setCurrencyCode( edd_get_currency() );

		if ( ! empty( $purchase_data['user_info']['address'] ) ) {
			$sofortueberweisung->setSenderCountryCode( $purchase_data['user_info']['address']['country'] );
		}

		$sofortueberweisung->setSuccessUrl( $this->_getSuccessURL( $payment_id ), true );
		$sofortueberweisung->setAbortUrl( $this->_getFailureURL( $payment_id ) );

		$listener_url = $this->_getListenerURL();
		$statuses     = array( 'loss', 'pending', 'received', 'refunded', 'untraceable' );
		$sofortueberweisung->setNotificationUrl( $listener_url, implode( ',', $statuses ) );

		if ( edd_get_option( 'sofortueberweisung_protection', false ) ) {
			$sofortueberweisung->setCustomerprotection( true );
		}

		$sofortueberweisung->sendRequest();

		 $this->log( sprintf( 'Doing Request with purchase data: %s  Payment data: %s Sofort Object: %s', var_export( $purchase_data, true ), var_export( $payment_data, true ) , var_export( $sofortueberweisung, true ) ) );

		if ( $sofortueberweisung->isError() ) {
			$this->_handlePaymentError( $payment_id, sprintf( __( 'Sofortueberweisung %1$s. Request data: %2$s', 'wpenon' ), $sofortueberweisung->getError(), json_encode( $sofortueberweisung->getData() ) ), true );
		}

		$this->_handlePaymentSuccess( $payment_id, $sofortueberweisung->getTransactionId(), $sofortueberweisung->getPaymentUrl() );
	}

	private function add_active_transaction( $transaction_id ) {
		$transactions = $this->load_transactions();
		if( NULL === $transactions ) {
			$transactions = array();
		}
		array_push( $transactions, $transaction_id );
		$this->save_transactions( $transactions );
	}

	private function del_active_transaction( $transaction_id ) {
		$transactions = $this->load_transactions();
		$key = array_search( $transaction_id, $transactions );
		unset( $transactions[ $key ] );
		$this->save_transactions( $transactions );
	}

	private function is_transaction_active( $transaction_id ) {
		$transactions = $this->load_transactions();
		if ( in_array( $transaction_id, $transactions, true ) ) {
			return true;
		}

		return false;
	}

	private function load_transactions() {
		$transactions = get_option( 'edd_sofort_active_transactions' );
		$transactions = json_decode( $transactions, true );
		return $transactions;
	}

	private function save_transactions( $transactions ) {
		$transactions = json_encode( $transactions );
		update_option( 'edd_sofort_active_transactions', $transactions );
	}

	public function processPurchaseNotification( $data ) {
		$input = file_get_contents( 'php://input' );

		if ( empty( $input ) ) {
			$this->_handlePaymentProcessError( null, __( 'Missing POST data.', 'wpenon' ), true );
		}

		$this->log( sprintf( 'Incoming process purchase notification: %s', var_export( $input, true ) ) );

		$notification = new \Sofort\SofortLib\Notification();

		$transaction_id = $notification->getNotification( $input );

		if( $this->is_transaction_active( $transaction_id ) ) {
			$this->_handlePaymentProcessError( null, sprintf( 'Interrupting! Transaction is already in process: %s', $transaction_id ), true );
		}
		$this->add_active_transaction( $transaction_id );

		if ( ! $transaction_id ) {
			$this->_handlePaymentProcessError( null, __( 'No transaction ID was submitted.', 'wpenon' ), true );
		}

		$payment_id = $this->_getPaymentIDByTransactionID( $transaction_id );

		if ( $payment_id < 1 ) {
			$this->_handlePaymentProcessError( null, sprintf( __( 'Payment for the transaction ID %s could not be found.', 'wpenon' ), $transaction_id ), true );
		}

		$config_key = $this->get_config_key();

		if ( substr_count( $config_key, ':' ) != 2 ) {
			$this->_handlePaymentProcessError( $payment_id, __( 'Invalid or missing config key.', 'wpenon' ), true );
		}

		$status_mappings = array(
			'loss'        => 'failed',
			'pending'     => 'pending',
			'received'    => 'publish',
			'refunded'    => 'refunded',
			'untraceable' => 'publish', // 'untraceable' is used for accounts without exact bank data connections.
		);

		if ( ! edd_get_option( 'sofortueberweisung_realtime', false ) ) {
			$status_mappings['pending'] = 'publish';
		}

		$transaction_data = new \Sofort\SofortLib\TransactionData( $config_key );
		$transaction_data->addTransaction( $transaction_id );

		$transaction_data->sendRequest();

		$transaction_status = $transaction_data->getStatus();
		if ( ! isset( $status_mappings[ $transaction_status ] ) ) {
			$this->_handlePaymentProcessError( $payment_id, __( 'Invalid status parameter in transaction data.', 'wpenon' ), true );
		}
		sleep(5);
		$this->del_active_transaction( $transaction_id );
		$this->_handlePaymentProcessSuccess( $payment_id, $status_mappings[ $transaction_status ] );
	}

	public function _registerSettings( $settings ) {
		$new_settings = array(
			'sofortueberweisung'            => array(
				'id'   => 'sofortueberweisung',
				'name' => '<span class="field-section-title">' . __( 'Sofortüberweisung Einstellungen', 'wpenon' ) . '</span>',
				'desc' => __( 'Konfigurieren Sie die Einstellungen für die Sofortüberweisung.', 'wpenon' ),
				'type' => 'header',
			),
			'sofortueberweisung_config_key' => array(
				'id'   => 'sofortueberweisung_config_key',
				'name' => __( 'Sofortüberweisung Konfigurationsschlüssel', 'wpenon' ),
				'desc' => __( 'Geben Sie den Konfigurationsschlüssel für Ihr Projekt bei Sofortüberweisung ein.', 'wpenon' ),
				'type' => 'text',
				'size' => 'regular',
			),
			'sofortueberweisung_protection' => array(
				'id'   => 'sofortueberweisung_protection',
				'name' => __( 'Sofortüberweisung Käuferschutz', 'wpenon' ),
				'desc' => __( 'Klicken Sie diese Checkbox an, um den Käuferschutz zu aktivieren. Achten Sie darauf, dass Sie in diesem Fall entsprechende Berechtigungen bei Sofortüberweisung haben müssen.', 'wpenon' ),
				'type' => 'checkbox',
			),
			'sofortueberweisung_realtime'   => array(
				'id'   => 'sofortueberweisung_realtime',
				'name' => __( 'Sofortüberweisung Echtzeit-Benachrichtigungen unterstützt?', 'wpenon' ),
				'desc' => __( 'Klicken Sie diese Checkbox an, wenn Ihr Projekt Echtzeit-Benachrichtigungen unterstützt. Dazu müssen Sie bei Sofortüberweisung ein Konto bei der Deutschen Handlesbank hinterlegt haben.', 'wpenon' ),
				'type' => 'checkbox',
			),
		);

		return array_merge( $settings, $new_settings );
	}

	public function _registerIcon( $icons ) {
		$icons['sofortueberweisung'] = 'SOFORT';

		return $icons;
	}
}
