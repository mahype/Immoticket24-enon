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

use Enon\Models\Api\Out\Distributor_Schemas\Distributor_Schema_Interface;

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
	 * @var Distributor_Schema_Interface
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
	 * Constructor.
	 *
	 * @param Distributor_Schema_Interface $distributor_schema Schema file for distributor.
	 * @param Energieausweis_Old $energy_certificate Engergy certificate object.
	 * @param Logger             $logger             Logger object.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Distributor_Schema_Interface $distributor_schema, Energieausweis_Old $energy_certificate, Logger $logger ) {
		$this->distributor_schema = $distributor_schema;
		$this->energy_certificate = $energy_certificate;
		$this->logger             = $logger;

		parent::__construct( $distributor_schema->get_endpoint(), $logger );
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

		$data = $this->distributor_schema->filter_data( $data );

		return $data;
	}

	/**
	 * Send data.
	 *
	 * @return bool True if energy certificate was sent.
	 *
	 * @since 1.0.0
	 */
	public function send() {
		$debug_values = array(
			'energy_certificate_id' => (int) $this->energy_certificate->id,
			'reseller_id'           => (int) $this->energy_certificate->reseller_id,
			'endpoint'              => $this->distributor_schema->get_endpoint(),
		);

		if ( ! $this->distributor_schema->check() ) {
			$this->logger()->notice( 'No data sent, because check not passed.', $debug_values );
			return false;
		}

		$this->logger()->notice( 'Start Sending data.', $debug_values );

		$this->post();

		$this->distributor_schema->set_sent();

		return true;
	}
}
