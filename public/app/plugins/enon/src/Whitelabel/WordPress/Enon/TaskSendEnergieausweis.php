<?php

namespace Enon\Whitelabel\WordPress\Enon;

use Awsm\WPWrapper\BuildingPlans\Actions;
use Awsm\WPWrapper\BuildingPlans\Task;
use Enon\Logger;
use Enon\Traits\Logger as LoggerTrait;
use Enon\Whitelabel\Reseller;
use Enon\Whitelabel\WordPress\Plugins\EddPayment;
use WPENON\Model\Energieausweis;

/**
 * Class SendEnergieausweis
 *
 * @since 1.0.0
 */
class TaskSendEnergieausweis implements Actions, Task {
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
	public function run()
	{
		$this->addActions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function addActions()
	{
		add_action( 'edd_update_payment_status', [ $this, 'sendData' ], 10, 2 );
	}

	/**
	 * Send data.
	 *
	 * @since 1.0.0
	 *
	 * @param int $paymentId Payment id.
	 * @param string $status Payment status.
	 */
	public function sendData( $paymentId, $status )
	{
		$endpoint = $this->reseller->data()->getPostEndpoint();

		// Do not anything if not payed.
		if ( 'publish' !== $status || empty( $endpoint ) ) {
			return;
		}

		$senderClassName = 'Enon\\Whitelabel\\PostData\\' . $this->reseller->data()->getPostDataConfigClass();

		if( ! class_exists( $senderClassName ) ) {
			$this->logger()->warning( sprintf( 'Sender Class %s does not exist, Do not send data.', $senderClassName ) );
			return;
		}

		$energieausweisId = (new EddPayment( $paymentId))->getEnergieausweisId();
		$energieausweis = new Energieausweis( $energieausweisId );

		( new $senderClassName( $endpoint, $energieausweis, $this->logger() ) )->send();
	}
}
