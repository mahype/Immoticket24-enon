<?php

namespace Enon\Whitelabel\WordPress\Enon;

use Enon\Logger;
use Enon\Traits\Logger as LoggerTrait;
use Enon\Whitelabel\Reseller;

/**
 * Class SendEnergieausweis
 *
 * @since 1.0.0
 */
class SendEnergieausweis {
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
}
