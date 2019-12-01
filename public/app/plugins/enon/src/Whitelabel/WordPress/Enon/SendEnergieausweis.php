<?php

namespace Enon\Whitelabel\WordPress\Enon;

use Awsm\WPWrapper\BuildingPlans\Actions;
use Awsm\WPWrapper\BuildingPlans\Task;
use Enon\Logger;
use Enon\Traits\Logger as LoggerTrait;
use Enon\Whitelabel\Reseller;
use WPENON\Model\Energieausweis;

/**
 * Class SendEnergieausweis
 *
 * @since 1.0.0
 */
class SendEnergieausweis implements Actions, Task {
	use LoggerTrait;

	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 * @var Reseller;
	 */
	private $reseller;

	/**
	 * SendEnergieausweis constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller Reseller object.
	 * @param Logger $logger Logger object.
	 */
	public function __construct( Reseller $reseller, Logger $logger )
	{
		$this->reseller = $reseller;
		$this->logger = $logger;
	}

	/**
	 * Running task.
	 *
	 * @since 1.0.0
	 * 
	 * @return mixed|void
	 */
	public function run() {
		// No endpoint, no data.
		if( empty( $this->reseller->data()->getPostEndpoint() ) ) {
			return;
		}

		$this->addActions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function addActions()
	{
		add_action( 'wpenon_payment_successful', [ $this, 'sendData' ] );
	}

	/**
	 * Get energieausweis id by payment id.
	 *
	 * @since 1.0.0
	 *
	 * @param int $paymentId Payment id.
	 * @return int Energieausweis id.
	 *
	 * @todo Move this to energieausweis section.
	 */
	private function getEnergieausweisIdByPaymentId( $paymentId )
	{
		$payment_meta = edd_get_payment_meta( $paymentId  );
		$item = array_shift( $payment_meta['cart_details'] );
		return $item[ 'id' ];
	}

	/**
	 * Send data.
	 *
	 * @since 1.0.0
	 *
	 * @param int $paymentId Payment id.
	 */
	public function sendData( $paymentId )
	{
		$senderClassName = $this->reseller->data()->getPostDataConfigClass();
		$senderClassName = 'Enon\\Whitelabel\\PostData\\' . $senderClassName;

		if( ! class_exists( $senderClassName ) ) {
			$this->logger()->warning( sprintf( 'Sender Class %s does not exist, Do not send data.', $senderClassName ) );
			return;
		}

		$energieausweis = new Energieausweis( $this->getEnergieausweisIdByPaymentId( $paymentId) );

		$senderEndpoint = $this->reseller->data()->getPostEndpoint();
		$postEnergieausweis = new $senderClassName( $senderEndpoint, $energieausweis, $this->logger() );
		$postEnergieausweis->send();
	}
}
