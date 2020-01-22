<?php
/**
 * Distributor for sending energy certificates to other API's.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Transfer
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Api\Out;

use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Tools\Logger_Trait;

use Enon\Models\Api\Out\Distributor_Schemas\Distributor_Schema;

use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Distributor_Energy_Certificate
 *
 * @since 1.0.0
 */
class Distributor_Energy_Certificate extends Request {
	use Logger_Trait;

	/**
	 * Distributor schema.
	 *
	 * @var Distributor_Schema
	 *
	 * @since 1.0.0
	 */
	private $distributor_schema;

	/**
	 * Energieausweis object.
	 *
	 * @var Energieausweis_Old
	 *
	 * @since 1.0.0
	 */
	private $energy_certificate;

	/**
	 * Logger object.
	 *
	 * @var Logger
	 *
	 * @since 1.0.0
	 */
	private $logger;

	/**
	 * Constructor.
	 *
	 * @param Distributor_Schema $distributor_schema Schema file for distributor.
	 * @param Energieausweis_Old $energy_certificate Engergy certificate object.
	 * @param Logger             $logger             Logger object.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Distributor_Schema $distributor_schema, Energieausweis_Old $energy_certificate, Logger $logger ) {
		$this->distributor_schema = $distributor_schema;
		$this->energy_certificate = $energy_certificate;
		$this->logger             = $logger;
	}

	/**
	 * Preparing data.
	 *
	 * @since 1.0.0
	 */
	protected function get_body() {
		$fields = $this->energy_certificate->getSchema()->getFields();

		$data = array();

		foreach ( $fields as  $key => $field ) {
			$data[ $key ] = $this->energy_certificate->$key;
		}

		$this->distributor_schema->filter_data( $data );
	}

	/**
	 * Send data.
	 *
	 * @return bool True if energy certificate was sent.
	 *
	 * @since 1.0.0
	 */
	public function send() {
		if ( ! $this->distributor_schema->check() ) {
			$this->logger()->notice( sprintf( 'Energy certificate #%s: No data sent to %s, because check not passed.', $this->energy_certificate->id, $this->distributor_schema->get_endpoint() ) );
			return false;
		}

		$this->post();

		return true;
	}
}
