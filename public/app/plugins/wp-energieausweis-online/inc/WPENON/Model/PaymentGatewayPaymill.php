<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

class PaymentGatewayPaymill extends \WPENON\Model\PaymentGateway {
	protected static $gateway_name = 'paymill';

	protected function __construct() {
		$this->gateway_args = array(
			'admin_label'    => __( 'Lastschrift (Paymill)', 'wpenon' ),
			'checkout_label' => __( 'Lastschrift', 'wpenon' ),
		);

		add_action( 'wp_footer', array( $this, '_enqueueScript' ) );
		add_filter( 'edd_settings_gateways', array( $this, '_registerSettings' ) );
		add_action( 'init', array( $this, '_maybeInstallWebhook' ) );

		add_action( 'edd_paymill_cc_form', array( $this, '_displayForm' ) );

		add_filter( 'wpenon_customer_meta_fields', array( $this, '_clientIDMetaField' ) );

		$this->listener_key = 'PAYMILL';

		parent::__construct();
	}

	public function processPurchase( $purchase_data ) {
		$payment_data = $this->_makePaymentData( $purchase_data );

		// Record the pending payment
		$payment_id = edd_insert_payment( $payment_data );

		if ( ! $payment_id ) {
			$this->_handlePaymentError( $payment_id, sprintf( __( 'Payment creation failed while processing a Paymill purchase. Payment data: %s', 'wpenon' ), json_encode( $payment_data ) ), true );
		}

		$api_key = $this->_getKey( 'private' );
		if ( ! $api_key ) {
			$this->_handlePaymentError( $payment_id, __( 'Paymill purchase could not be processed because of missing private key.', 'wpenon' ), true );
		}

		if ( ! isset( $purchase_data['post_data']['paymill_token'] ) ) {
			$this->_handlePaymentError( $payment_id, __( 'Paymill purchase could not be processed because no purchase token has been provided.', 'wpenon' ), true );
		}

		$token = esc_html( $purchase_data['post_data']['paymill_token'] );

		$client_id = '';

		$user_info = edd_get_payment_meta_user_info( $payment_id );
		if ( is_array( $user_info ) ) {
			$user_email = $user_info['email'];
			$customer   = EDD()->customers->get_customer_by( 'email', $user_email );
			if ( $customer ) {
				$client_id = \WPENON\Util\CustomerMeta::get( $customer->id, 'paymill_client_id', true );
				if ( empty( $client_id ) ) {
					$user_name = $user_info['first_name'] . ' ' . $user_info['last_name'];

					$request = new \Paymill\Request( $api_key );

					$client = new \Paymill\Models\Request\Client();
					$client->setDescription( $user_name );
					$client->setEmail( $user_email );

					try {
						$response  = $request->create( $client );
						$client_id = $response->getId();
					} catch ( \Exception $e ) {
					}

					if ( ! empty( $client_id ) ) {
						\WPENON\Util\CustomerMeta::update( $customer->id, 'paymill_client_id', $client_id );
					}
				}
			}
		}

		$request = new \Paymill\Request( $api_key );

		$transaction = new \Paymill\Models\Request\Transaction();
		$transaction->setDescription( \WPENON\Util\PaymentMeta::instance()->getDepositKey( $payment_id ) );
		$transaction->setAmount( $this->_sanitizeAmount( $purchase_data['price'] ) );
		$transaction->setCurrency( edd_get_currency() );
		$transaction->setToken( $token );
		if ( ! empty( $client_id ) ) {
			$transaction->setClient( $client_id );
		}

		$transaction_id = '';

		try {
			$response       = $request->create( $transaction );
			$transaction_id = $response->getId();
		} catch ( \Paymill\Services\PaymillException $e ) {
			$this->_handlePaymentError( $payment_id, sprintf( __( 'Paymill Error: %1$s: %2$s', 'wpenon' ), $e->getStatusCode(), $e->getErrorMessage() ), true );
		} catch ( \Exception $e ) {
			$this->_handlePaymentError( $payment_id, sprintf( __( 'Paymill PHP Error: %1$s: %2$s', 'wpenon' ), $e->getCode(), $e->getMessage() ), true );
		}

		$this->_handlePaymentSuccess( $payment_id, $transaction_id );
	}

	public function processPurchaseNotification( $data ) {
		$input = file_get_contents( 'php://input' );
		if ( empty( $input ) ) {
			$this->_handlePaymentProcessError( null, __( 'Missing POST data.', 'wpenon' ), true );
		}

		try {
			$data = json_decode( $input, true );
		} catch ( \Exception $e ) {
			$this->_handlePaymentProcessError( null, __( 'JSON parse error.', 'wpenon' ), true );
		}

		if ( ! isset( $data['event'] ) ) {
			$this->_handlePaymentProcessError( null, __( 'Missing transaction object.', 'wpenon' ), true );
		}

		$event = $data['event'];

		if ( ! isset( $event['event_resource'] ) || ! is_array( $event['event_resource'] ) ) {
			$this->_handlePaymentProcessError( null, __( 'Missing transaction object.', 'wpenon' ), true );
		}

		$transaction_id = '';

		if ( isset( $event['event_resource']['id'] ) ) {
			$transaction_id = $event['event_resource']['id'];
		} else {
			if ( ! isset( $event['event_resource']['transaction'] ) || ! is_array( $event['event_resource']['transaction'] ) ) {
				$this->_handlePaymentProcessError( null, __( 'Missing transaction object.', 'wpenon' ), true );
			}
			$transaction_id = $event['event_resource']['transaction']['id'];
		}

		$payment_id = $this->_getPaymentIDByTransactionID( $transaction_id );

		if ( $payment_id < 1 ) {
			$this->_handlePaymentProcessError( null, sprintf( __( 'Payment for the transaction ID %s could not be found.', 'wpenon' ), $transaction_id ), true );
		}

		$status_mappings = array(
			'chargeback.executed'   => 'refunded',
			'transaction.pending'   => 'pending',
			'transaction.succeeded' => 'publish',
			'transaction.failed'    => 'failed',
		);
		if ( ! isset( $event['event_type'] ) || ! isset( $status_mappings[ $event['event_type'] ] ) ) {
			$this->_handlePaymentProcessError( $payment_id, __( 'Invalid event type provided.', 'wpenon' ), true );
		}

		$this->_handlePaymentProcessSuccess( $payment_id, $status_mappings[ $event['event_type'] ] );
	}

	public function _enqueueScript() {
		if ( edd_is_checkout() && edd_is_gateway_active( static::$gateway_name ) ) {
			$public_key = $this->_getKey( 'public' );
			if ( $public_key ) {
				?>
				<script type="text/javascript">
					var PAYMILL_PUBLIC_KEY = '<?php echo esc_html( $public_key ); ?>';
				</script>
				<?php
			}
			?>
			<script type="text/javascript" src="https://bridge.paymill.com/"></script>
			<script type="text/javascript" src="<?php echo WPENON_URL; ?>/assets/iban.js"></script>
			<script type="text/javascript" src="<?php echo WPENON_URL; ?>/assets/paymill.js"></script>
			<?php
		}
	}

	public function _registerSettings( $settings ) {
		$new_settings = array(
			'paymill'                  => array(
				'id'   => 'paymill',
				'name' => '<span class="field-section-title">' . __( 'Paymill Einstellungen', 'wpenon' ) . '</span>',
				'desc' => __( 'Konfigurieren Sie die Einstellungen für das automatische Lastschriftverfahren mit Paymill.', 'wpenon' ),
				'type' => 'header',
			),
			'paymill_live_public_key'  => array(
				'id'   => 'paymill_live_public_key',
				'name' => __( 'Paymill Live Public Key', 'wpenon' ),
				'desc' => __( 'Geben Sie den öffentlichen Schlüssel für Ihr Paymill-Konto (Live-Modus) ein.', 'wpenon' ),
				'type' => 'text',
				'size' => 'regular',
			),
			'paymill_live_private_key' => array(
				'id'   => 'paymill_live_private_key',
				'name' => __( 'Paymill Live Private Key', 'wpenon' ),
				'desc' => __( 'Geben Sie den privaten Schlüssel für Ihr Paymill-Konto (Live-Modus) ein.', 'wpenon' ),
				'type' => 'text',
				'size' => 'regular',
			),
			'paymill_test_public_key'  => array(
				'id'   => 'paymill_test_public_key',
				'name' => __( 'Paymill Test Public Key', 'wpenon' ),
				'desc' => __( 'Geben Sie den öffentlichen Schlüssel für Ihr Paymill-Konto (Test-Modus) ein.', 'wpenon' ),
				'type' => 'text',
				'size' => 'regular',
			),
			'paymill_test_private_key' => array(
				'id'   => 'paymill_test_private_key',
				'name' => __( 'Paymill Test Private Key', 'wpenon' ),
				'desc' => __( 'Geben Sie den privaten Schlüssel für Ihr Paymill-Konto (Test-Modus) ein.', 'wpenon' ),
				'type' => 'text',
				'size' => 'regular',
			),
		);

		return array_merge( $settings, $new_settings );
	}

	public function _maybeInstallWebhook() {
		if ( edd_is_gateway_active( static::$gateway_name ) && ! get_transient( 'wpenon_paymill_webhook_id' ) ) {
			$api_key = $this->_getKey( 'private' );
			if ( $api_key ) {
				$request = new \Paymill\Request( $api_key );

				$listener_url = $this->_getListenerURL();

				$webhook_query = new \Paymill\Models\Request\Webhook();
				$webhook_query->setFilter( array( 'url' => $listener_url ) );

				$webhooks = array();

				try {
					$webhooks = $request->getAllAsModel( $webhook_query );
				} catch ( \Paymill\Services\PaymillException $e ) {

				} catch ( \Exception $e ) {

				}

				$webhook_found = false;
				if ( count( $webhooks ) > 0 ) {
					$webhook = array_shift( $webhooks );
					if ( count( $webhooks ) > 0 ) {
						foreach ( $webhooks as $hook ) {
							$delete_hook = new \Paymill\Models\Request\Webhook();
							$delete_hook->setId( $hook->getId() );
							$response = $request->delete( $delete_hook );
						}
					}
					if ( $webhook->getActive() ) {
						$webhook_found = true;
						set_transient( 'wpenon_paymill_webhook_id', $webhook->getId(), DAY_IN_SECONDS );
					} else {
						$update_hook = new \Paymill\Models\Request\Webhook();
						$update_hook->setId( $webhook->getId() );
						$update_hook->setUrl( $listener_url );
						$update_hook->setEventTypes( array(
							'chargeback.executed',
							'transaction.pending',
							'transaction.succeeded',
							'transaction.failed',
						) );
						$update_hook->setActive( true );
						try {
							$response      = $request->update( $update_hook );
							$webhook_found = true;
							set_transient( 'wpenon_paymill_webhook_id', $response->getId(), DAY_IN_SECONDS );
						} catch ( \Paymill\Services\PaymillException $e ) {

						} catch ( \Exception $e ) {

						}
					}
				}

				if ( ! $webhook_found ) {
					$webhook = new \Paymill\Models\Request\Webhook();
					$webhook->setUrl( $listener_url );
					$webhook->setEventTypes( array(
						'chargeback.executed',
						'transaction.pending',
						'transaction.succeeded',
						'transaction.failed',
					) );

					try {
						$response = $request->create( $webhook );
						set_transient( 'wpenon_paymill_webhook_id', $response->getId(), DAY_IN_SECONDS );
					} catch ( \Paymill\Services\PaymillException $e ) {

					} catch ( \Exception $e ) {

					}
				}
			}
		}
	}

	public function _displayForm() {
		$enable_placeholders = apply_filters( 'wpenon_enable_purchase_placeholders', true );

		do_action( 'edd_before_cc_fields' );
		?>
		<fieldset id="wpenon_sepa_fields">
			<span><legend><?php _e( 'Kontodaten', 'wpenon' ); ?></legend></span>
			<?php if ( is_ssl() ) : ?>
				<div id="edd_secure_site_wrapper">
					<span class="padlock"></span>
					<span><?php _e( 'This is a secure SSL encrypted payment.', 'easy-digital-downloads' ); ?></span>
				</div>
			<?php endif; ?>
			<p id="wpenon-sepa-account-holder-wrap">
				<label for="sepa_account_holder" class="edd-label">
					<?php _e( 'Kontoinhaber', 'wpenon' ); ?>
					<span class="edd-required-indicator">*</span>
				</label>
				<span class="edd-description"><?php _e( 'Der Name des Kontoinhabers.', 'wpenon' ); ?></span>
				<input type="text" autocomplete="off" id="sepa_account_holder"
				       class="sepa-account-holder edd-input required"<?php echo $enable_placeholders ? ' placeholder="' . __( 'Kontoinhaber', 'wpenon' ) . '"' : ''; ?>>
			</p>
			<p id="wpenon-sepa-account-id-wrap">
				<label for="sepa_account_id" class="edd-label">
					<?php _e( 'IBAN', 'wpenon' ); ?>
					<span class="edd-required-indicator">*</span>
				</label>
				<span class="edd-description"><?php _e( 'Die IBAN für das Bankkonto.', 'wpenon' ); ?></span>
				<input type="text" autocomplete="off" id="sepa_account_id"
				       class="sepa-account-id edd-input required"<?php echo $enable_placeholders ? ' placeholder="' . __( 'IBAN', 'wpenon' ) . '"' : ''; ?>>
			</p>
			<p id="wpenon-sepa-bank-id-wrap">
				<label for="sepa_bank_id" class="edd-label">
					<?php _e( 'BIC', 'wpenon' ); ?>
					<span class="edd-required-indicator">*</span>
				</label>
				<span class="edd-description"><?php _e( 'Die BIC für das Bankkonto.', 'wpenon' ); ?></span>
				<input type="text" autocomplete="off" id="sepa_bank_id"
				       class="sepa-bank-id edd-input required"<?php echo $enable_placeholders ? ' placeholder="' . __( 'BIC', 'wpenon' ) . '"' : ''; ?>>
			</p>
			<p id="wpenon-sepa-bank-name-wrap" style="display:none;">
				<label for="sepa_bank_name" class="edd-label">
					<?php _e( 'Kreditinstitut', 'wpenon' ); ?>
				</label>
				<span class="edd-description"><?php _e( 'Das Kreditinstitut für das Bankkonto.', 'wpenon' ); ?></span>
				<input type="text" autocomplete="off" id="sepa_bank_name"
				       class="sepa-bank-name edd-input"<?php echo $enable_placeholders ? ' placeholder="' . __( 'Kreditinstitut', 'wpenon' ) . '"' : ''; ?>
				       readonly disabled>
			</p>
		</fieldset>
		<?php
		do_action( 'edd_after_cc_fields' );
	}

	public function _clientIDMetaField( $fields ) {
		$fields['paymill_client_id'] = __( 'Paymill Client ID', 'wpenon' );

		return $fields;
	}

	protected function _sanitizeAmount( $amount ) {
		//TODO: this is valid for EUR and some other currencies, but not all
		return absint( $amount * 100 );
	}

	protected function _getKey( $type ) {
		if ( $type != 'private' ) {
			$type = 'public';
		}
		$mode = edd_is_test_mode() ? 'test' : 'live';

		return edd_get_option( 'paymill_' . $mode . '_' . $type . '_key', false );
	}
}
