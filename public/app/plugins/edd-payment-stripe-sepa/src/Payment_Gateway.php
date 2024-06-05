<?php

namespace Awsm\Edd\Payment\Stripe_Sepa;

use Awsm\Edd\Payment\Edd_Payment_Gateway;
use Awsm\Edd\Payment\Gateway_Exception;
use Awsm\Edd\Payment\Validation_Exception;
use PHP_IBAN\IBAN;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * Class Payment_Gateway
 *
 * @package Awsm\Edd\Payment\StripeBACS
 *
 * @since 1.0.0
 */
class Payment_Gateway extends Edd_Payment_Gateway
{
	/**
	 * Setting up core values.
	 *
	 * @since 1.0.0
	 */
	public function setup()
	{
		$this->name           = __('SEPA Lastschrift (Stripe)', 'awsm-edd-stripe-sepa');
		$this->checkout_label = __('Lastschrift', 'awsm-edd-stripe-sepa');
		$this->slug = 'stripe_sepa';
		$this->show_form = true;
	}

	/**
	 * Register gateway settings.
	 *
	 * @param array $settings Gateway settings.
	 *
	 * @return array Filtered gateway settings.
	 *
	 * @since 1.0.0
	 */
	protected function settings_fields(array $settings = array()): array
	{
		$stripe_dashboard_url = 'https://dashboard.stripe.com/dashboard';

		$settings[$this->slug] = array(
			'header' => array(
				'id'   => 'stripe-bacs-header',
				'name' => '<strong>' . $this->name . '</strong>',
				'type' => 'header',
			),
			'description' => array(
				'id'   => 'amazon_register',
				'name' => __('Register with Stripe', 'awsm-edd-stripe-sepa'),
				'desc' => sprintf(__('<a href="%s" target="_blank">Get you public key and your secret</a> and start with Stripe.', 'awsm-edd-stripe-sepa'), $stripe_dashboard_url, $this->get_listener_url()),
				'type' => 'descriptive_text',
			),
			'webhooks' => array(
				'id'   => 'webhooks',
				'name' => __('Webhooks', 'awsm-edd-stripe-sepa'),
				'desc' => sprintf(__('In order for Stripe to function completely, you must configure your Stripe webhooks. Visit your account dashboard to configure them. Please add a webhook endpoint for the URL below.<br />Webhook URL: <code>%s</code>.', 'awsm-edd-stripe-sepa'), $this->get_listener_url()),
				'type' => 'descriptive_text',
			),
			'stripe_public_key' => array(
				'id'   => 'stripe_public_key',
				'name' => __('Public key', 'awsm-edd-stripe-sepa'),
				'desc' => __('Found on stripe dashboard after login.', 'awsm-edd-stripe-sepa'),
				'type' => 'text',
				'size' => 'regular',
			),
			'stripe_secret' => array(
				'id'   => 'stripe_secret',
				'name' => __('Secret', 'awsm-edd-stripe-sepa'),
				'desc' => __('Found on stripe dashboard after login.', 'awsm-edd-stripe-sepa'),
				'type' => 'text',
				'size' => 'regular',
			),
			'stripe_webhook_secret' => array(
				'id'   => 'stripe_webhook_secret',
				'name' => __('Webhook Secret', 'awsm-edd-stripe-sepa'),
				'desc' => __('Found on stripe dashboard after login.', 'awsm-edd-stripe-sepa'),
				'type' => 'text',
				'size' => 'regular',
			),
			'stripe_test_public_key' => array(
				'id'   => 'stripe_test_public_key',
				'name' => __('Public key (Test)', 'awsm-edd-stripe-sepa'),
				'desc' => __('Found on stripe dashboard after login.', 'awsm-edd-stripe-sepa'),
				'type' => 'text',
				'size' => 'regular',
			),
			'stripe_test_secret' => array(
				'id'   => 'stripe_test_secret',
				'name' => __('Secret (Test)', 'awsm-edd-stripe-sepa'),
				'desc' => __('Found on stripe dashboard after login.', 'awsm-edd-stripe-sepa'),
				'type' => 'text',
				'size' => 'regular',
			),
			'stripe_test_webhook_secret' => array(
				'id'   => 'stripe_test_webhook_secret',
				'name' => __('Webhook Secret (Test)', 'awsm-edd-stripe-sepa'),
				'desc' => __('Found on stripe dashboard after login.', 'awsm-edd-stripe-sepa'),
				'type' => 'text',
				'size' => 'regular',
			),
			'stripe_show_mandate_acceptance' => array(
				'id'   => 'stripe_show_mandate_acceptance',
				'name' => __('Mandate acceptance', 'awsm-edd-stripe-sepa'),
				'desc' => __('Show mandate acceptance', 'awsm-edd-stripe-sepa'),
				'type' => 'checkbox',
				'size' => 'regular',
			),
		);

		return $settings;
	}

	/**
	 * Get public key.
	 *
	 * @return string|bool Public key, false if there is no value.
	 *
	 * @since 1.0.0
	 */
	private function get_public_key()
	{
		if (edd_is_test_mode()) {
			$this->log('Using test mode public key.');
			return $this->get_setting('stripe_test_public_key');
		}

		return $this->get_setting('stripe_public_key');
	}

	/**
	 * Get secret.
	 *
	 * @return string|bool Secret, false if there is no value.
	 *
	 * @since 1.0.0
	 */
	private function get_secret()
	{
		if (edd_is_test_mode()) {
			return $this->get_setting('stripe_test_secret');
		}

		return $this->get_setting('stripe_secret');
	}

	/**
	 * Get webhook secret.
	 *
	 * @return string|bool Webhook secret, false if there is no value.
	 *
	 * @since 1.0.0
	 */
	private function get_webhook_secret()
	{
		if ( edd_is_test_mode() ) {
			return $this->get_setting('stripe_test_webhook_secret');
		}

		return $this->get_setting('stripe_webhook_secret');
	}

	/**
	 * Checkout scripts.
	 *
	 * @since 1.0.0
	 */
	protected function checkout_html()
	{
?>
		<fieldset id="sepa_fields">
			<span>
				<legend><?php _e('Kontodaten', 'awsm-edd-stripe-sepa'); ?></legend>
			</span>
			<p id="sepa-account-holder-wrap">
				<label for="sepa_account_holder" class="edd-label">
					<?php _e('Kontoinhaber', 'awsm-edd-stripe-sepa'); ?>
					<span class="edd-required-indicator">*</span>
				</label>
				<span class="edd-description"><?php _e('Der Name des Kontoinhabers.', 'awsm-edd-stripe-sepa'); ?></span>
				<input type="text" autocomplete="off" id="sepa_account_holder" name="sepa_account_holder" class="sepa-account-holder edd-input required">
			</p>
			<p id="sepa-account-holder-wrap">
				<label for="sepa_iban" class="edd-label">
					<?php _e('IBAN', 'awsm-edd-stripe-sepa'); ?>
					<span class="edd-required-indicator">*</span>
				</label>
				<span class="edd-description"><?php _e('Die IBAN des Kontos.', 'awsm-edd-stripe-sepa'); ?></span>
				<input type="text" autocomplete="off" id="sepa_iban" name="sepa_iban" class="sepa-account-holder edd-input required">
			</p>
			<?php if ((bool) $this->get_setting('stripe_show_mandate_acceptance')) : ?>
				<p>
					<?php echo sprintf(__('Indem Sie Ihre IBAN angeben und diese Zahlung bestätigen, ermächtigen Sie (A) %s und Stripe, unseren Zahlungsdienstleister, Ihrer Bank Anweisungen zur Belastung Ihres Kontos zu senden und (B) Ihre Bank, Ihr Konto gemäß diesen Anweisungen zu belasten. Sie haben Anspruch auf eine Rückerstattung von Ihrer Bank gemäß den Bedingungen Ihrer Vereinbarung mit Ihrer Bank. Eine Rückerstattung muss innerhalb von 8 Wochen ab dem Datum der Belastung Ihres Kontos beantragt werden.', 'awsm-edd-stripe-sepa'), get_option('blogname')); ?>
				</p>
			<?php endif; ?>
		</fieldset>
		<script>
			document.getElementById('edd_purchase_form_wrap').addEventListener('click', function() {
				document.getElementById('sepa_iban').addEventListener('input', function(e) {
					e.target.value = e.target.value.replace(/[^\dA-Z]/g, '').replace(/(.{4})/g, '$1 ').trim();
				});
			});
		</script>
<?php
		do_action('edd_after_cc_fields');
	}

	/**
	 * Validate data.
	 *
	 * @param array $post_data Posted data to validate.
	 *
	 * @throws Validation_Exception Exception on validation.
	 *
	 * @since 1.0.0
	 */
	protected function validate($post_data)
	{
		$account_holder = $post_data['sepa_account_holder'];
		$iban           = $post_data['sepa_iban'];

		if (empty($account_holder)) {
			throw new Validation_Exception(__('Bankkonto: Kontoinhaber fehlt.', 'awsm-edd-stripe-sepa'));
		}

		$iban = new IBAN($iban);

		if ($iban->Country() !== 'DE') {
			throw new Validation_Exception(__('Bankkonto: Nur deutsche Bankkonten erlaubt.', 'awsm-edd-stripe-sepa'));
		}

		if (!$iban->Verify()) {
			throw new Validation_Exception(__('Bankkonto: Ungültige IBAN.', 'awsm-edd-stripe-sepa'));
		}
	}

	/**
	 * Processing payment.
	 *
	 * @param array $purchase_data Purchase data.
	 * @param array $post_data     Posted data.
	 * @param int   $payment_id    Payment id.
	 *
	 * @throws Gateway_Exception|Validation_Exception Gateway_Exception on problems with payment provider,
	 *                                                Validation_Exception on data validation errors.
	 *
	 * @since 1.0.0
	 */
	public function process_payment(array $purchase_data, array $post_data, int $payment_id)
	{
		$this->validate($post_data);

		if (!$this->get_secret()) {
			throw new Gateway_Exception('Missing secret key for stripe payment.');
		}

		$account_holder = $post_data['sepa_account_holder'];
		$iban           = $post_data['sepa_iban'];

		try {
			$stripe = new StripeClient([
				'api_key' => $this->get_secret(),
				'stripe_version' => '2024-04-10'
			]);

			$intent = $stripe->paymentIntents->create([
				'amount' => (int) (EDD()->cart->get_total() * 100),
				'currency' => 'EUR',
				'confirm' => true,
				'payment_method_types' => ['sepa_debit'],
				'payment_method_data' => [
					'type' => 'sepa_debit',
					'billing_details' => [
						'name' => $account_holder,
						'email' => $purchase_data['user_email'],
					],
					'sepa_debit' => [
						'iban' => $iban,
					],
				],
				'mandate_data' => [
					'customer_acceptance' => [
						'type' => 'online',
						'online' => [
							'ip_address' => $_SERVER['REMOTE_ADDR'],
							'user_agent' => $_SERVER['HTTP_USER_AGENT'],
						]
					],
				],
				'metadata' => [
					'payment_id' => $payment_id,
				],
			]);

			$this->set_payment_transaction_id($payment_id, $intent->id);			
		} catch (ApiErrorException $e) {
			$this->log($e->getMessage(), 'error');

			if ($e->getStripeCode() === 'sepa_unsupported_account') {
				throw new Validation_Exception(__('Invalid IBAN.', 'awsm-edd-stripe-sepa'));
			}

			throw new Gateway_Exception($e->getMessage());
		}
	}

	/**
	 * Payment listener.
	 *
	 * @param array $input Incoming data.
	 *
	 * @since 1.0.0
	 */
	public function payment_listener($input)
	{
		ob_implicit_flush(); // Print out result without buffering.

		header('Content-Type: application/json');
		$input = @file_get_contents('php://input');
		$body = json_decode($input);
		$event = null;

		try {
			$webhook_secret = $this->get_webhook_secret();
			$server_signature = $_SERVER['HTTP_STRIPE_SIGNATURE'];

			$event = \Stripe\Webhook::constructEvent(
				$input,
				$server_signature,
				$webhook_secret
			);

		} catch (\Exception  $e) {
			$this->log('Failure on Webhook creation: ' . $e->getMessage(), 'error');
			http_response_code(400);
			echo json_encode([ 'error' => $e->getMessage() ]);
			exit();
		}

		$payment_intend = $event->data->object;

		$payment_id = $this->get_payment_id_by_transaction_id($payment_intend->id);
		
		switch ($event->type) {			
			case 'payment_intent.processing':			
				// Pending payments have to be treated as succeeded. Otherwise it's taking days for publishing.
			case 'payment_intent.succeeded':											
				if (!empty($payment_id)) {
					status_header(200);
					$this->payment_complete($payment_id);
					die(esc_html('EDD Stripe: ' . $event->type));
				} else {
					status_header(500);
					die('-2');
				}
				break;
			case 'payment_intent.failed':
			case 'payment_intent.canceled':
				if (!empty($payment_id)) {
					status_header(200);
					$this->payment_failed($payment_id);
					die(esc_html('EDD Stripe: ' . $event->type));
				} else {
					status_header(500);
					die('-2');
				}
				break;
			default:
				// Not processed event type
				status_header(200);
				$this->log('Not processed event type: ' . $event->type, 'error');
				die(esc_html('EDD Stripe: ' . $event->type));
		}

		exit();
	}
}
