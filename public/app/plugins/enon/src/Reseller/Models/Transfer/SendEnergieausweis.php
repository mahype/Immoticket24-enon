<?php

namespace Enon\Reseller\Models\Transfer;

use Enon\Logger;

use WPENON\Model\Energieausweis;


/**
 * Class ResellerSendEnergieausweis
 *
 * @since 1.0.0
 */
abstract class SendEnergieausweis extends Send {
	/**
	 * Energieausweis Object.
	 *
	 * @since 1.0.0
	 *
	 * @var Energieausweis
	 */
	protected $energieausweis;

	/**
	 * ResellerSendEnergieausweis constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $endpoint
	 * @param Energieausweis $energieausweis
	 * @param Logger $logger
	 */
	public function __construct( $endpoint, Energieausweis $energieausweis, Logger $logger )
	{
		$this->energieausweis = $energieausweis;

		parent::__construct( $endpoint, $logger );
	}
}
