<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

class PaymentGatewayDeposit extends \WPENON\Model\PaymentGateway {
	protected static $gateway_name = 'deposit';

	protected function __construct() {
		$this->gateway_args = array(
			'admin_label'    => __( 'Banküberweisung', 'wpenon' ),
			'checkout_label' => __( 'Banküberweisung', 'wpenon' ),
			'supports'       => array( 'manual_handling' ),
		);

		$this->show_cc_form = false;

		parent::__construct();
	}

	public function processPurchase( $purchase_data ) {
		$payment_data = $this->_makePaymentData( $purchase_data );

		// Record the pending payment
		$payment_id = edd_insert_payment( $payment_data );

		if ( ! $payment_id ) {
			$this->_handlePaymentError( $payment_id, sprintf( __( 'Payment creation failed while processing a deposit purchase. Payment data: %s', 'wpenon' ), json_encode( $payment_data ) ), true );
		}

		$redirect_url = apply_filters( 'wpenon_payment_success_url', home_url('/vermarktungspaket-immoticket24-2/?payment-confirmation=deposit&payment-id=' . $payment_id ), $payment_id );

		$this->_handlePaymentSuccess( $payment_id, null, $redirect_url );
	}	
	
	public function processPurchaseNotification( $data ) {
		// there is no automated purchase notification for a deposit
	}
}
