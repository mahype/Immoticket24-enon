<?php
/**
 * Task which loads reseller email sctripts to system.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Tools\Logger_Trait;

use Enon\Models\Api\Out\Distributor_Energy_Certificate;
use Enon\Models\Api\Out\Distributor_Schemas\Sparkasse_Schema;
use Enon_Reseller\Models\Reseller;
use Enon_Reseller\Models\Reseller_Data;
use Enon\Models\Edd\Payment;

use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Submit_Energieausweis
 *
 * @since 1.0.0
 */
class Add_Energy_Certificate_Submission implements Actions, Task {
	use Logger_Trait;

	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 * @var Reseller;
	 */
	private $reseller;

	/**
	 * Send_Energieausweis constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller Reseller object.
	 * @param Logger   $logger Logger object.
	 */
	public function __construct( Reseller $reseller, Logger $logger ) {
		$this->reseller = $reseller;
		$this->logger   = $logger;
	}

	/**
	 * Running task.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'edd_update_payment_status', array( $this, 'send_data_after_payment_completed' ), 10, 2 );
		// add_action( 'edd_payment_saved', array( $this, 'send_data_after_payment_saved' ), 10, 1 ); // For testing purposes
	}

	/**
	 * Send data after a completed payment.
	 *
	 * @param int    $payment_id Payment id.
	 * @param string $status     Status of payment.
	 *
	 * @since 1.0.0
	 */
	public function send_data_after_payment_completed( int $payment_id, string $status ) {
		if ( 'publish' !== $status ) {
			return;
		}

		$this->send_data_by_payment_id( $payment_id );
	}

	/**
	 * Send data after a saved payment (for test purpose).
	 *
	 * @param int          $payment_id Payment id.
	 * @param \EDD_Payment $payment    Edd Payment object.
	 *
	 * @since 1.0.0
	 */
	public function send_data_after_payment_saved( int $payment_id ) {
		$this->send_data_by_payment_id( $payment_id );
	}

	/**
	 * Sending data by payment id.
	 *
	 * @param int  $payment_id Payment id.
	 *
	 * @since 1.0.0
	 */
	public function send_data_by_payment_id( int $payment_id ) {
		$payment = new Payment( $payment_id );

		$energieausweis_id = $payment->get_energieausweis_id();
		$energieausweis    = new Energieausweis_Old( $energieausweis_id );
		$reseller_id       = $energieausweis->reseller_id;

		if ( empty( $reseller_id ) ) {
			$values = array(
				'energy_certificate_id' => $energieausweis->id,
			);

			$this->logger()->notice( 'No reseller id given. No data sent to reseller for energy certificate.', $values );
			return;
		}

		$this->send_data( $energieausweis, $reseller_id );
	}

	/**
	 * Send data.
	 *
	 * @param Energieausweis_Old $energieausweis Energieausweis object.
	 * @param int                $reseller_id    Reseller id.
	 *
	 * @since 1.0.0
	 */
	public function send_data( Energieausweis_Old $energieausweis, int $reseller_id ) {
		$reseller_data = new Reseller_Data( $reseller_id );

		$schema_name  = ucfirst( $reseller_data->general->get_company_id() );

		$debug_values = array(
			'energy_certificate_id' => $energieausweis->id,
			'reseller_id'           => $reseller_id,
			'schema_name'          => $schema_name,
		);

		if ( empty( $schema_name ) ) {
			$this->logger()->warning( 'Company has no id.', $debug_values );
			return;
		}

		$schema_class = 'Enon_Reseller\\Models\\Api\\Out\\Distributor_Schemas\\' . $schema_name;

		// Is there an schema name which was set? Bail out if not.
		if ( ! class_exists( $schema_class ) ) {
			$debug_values = array(
				'energy_certificate_id' => $energieausweis->id,
				'reseller_id'           => $reseller_id,
				'schema_class'          => $schema_class,
			);

			$this->logger()->warning( 'Sender Class does not exist. No data sent to reseller for energy certificate.', $debug_values );
			return;
		}

		$schema   = new $schema_class();

		// Is there an endpoint to send the data? Bail out if not.
		if ( empty( $schema->get_endpoint() ) ) {
			$values = array(
				'reseller_id'           => $reseller_id,
				'energy_certificate_id' => $energieausweis->id,
			);

			$this->logger()->notice( 'No endpoint given. No data sent to reseller for energy certificate.', $values );
			return;
		}

		$distributor = new Distributor_Energy_Certificate( $schema, $energieausweis, $this->logger() );
		$distributor->send();
	}
}
