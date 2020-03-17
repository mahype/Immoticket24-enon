<?php

/**
 * Distributor schema parent class.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Transfer
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Api\Out\Distributor_Schemas;

use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Tools\Logger_Trait;
use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Distributor_Schema
 *
 * @since 1.0.0
 */
abstract class Distributor_Schema implements Distributor_Schema_Interface {
	use Logger_Trait;

	/**
	 * Energieausweis object.
	 *
	 * @var Energieausweis_Old
	 *
	 * @socne 1.0.0
	 */
	protected $energieausweis;

	/**
	 * Distrubutor schema constructor
	 *
	 * @param Logger $logger Logger object.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Logger $logger, Energieausweis_Old $energieausweis ) {
		$this->logger         = $logger;
		$this->energieausweis = $energieausweis;
	}
}
