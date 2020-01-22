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

namespace Enon_Reseller\Tasks\Enon;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Tools\Logger_Trait;

use Enon\Models\Api\Out\Distributor_Energy_Certificate;
use Enon\Models\Api\Out\Distributor_Schemas\Sparkasse_Schema;
use Enon_Reseller\Models\Reseller;
use Enon_Reseller\Models\Reseller_Data;
use Enon\Edd\Models\Payment;

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
		add_action( 'edd_update_payment_status', array( $this, 'send_data' ), 10, 2 );
	}

	/**
	 * Send data.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $payment_id Payment id.
	 * @param string $status Payment status.
	 */
	public function send_data( $payment_id, $status ) {
		if ( 'publish' !== $status ) {
			return;
		}

		$payment = new Payment( $payment_id );

		$energieausweis_id  = $payment->get_energieausweis_id();
		$energieausweis     = new Energieausweis_Old( $energieausweis_id );
		$reseller_id        = $energieausweis->reseller_id;

		// Was there a reseller? Bail out if not.
		if ( empty( $reseller_id ) ) {
			return;
		}

		$reseller_data = new Reseller_Data( $reseller_id );
		$endpoint      = $reseller_data->send_data->get_post_endpoint();

		// Is there an endpoint to send the data? Bail out if not.
		if ( empty( $endpoint ) ) {
			return;
		}

		$schema_name  = $reseller_data->send_data->get_post_data_config_class();
		$schema_class = 'Enon_Reseller\\Models\\Api\\Schemas\\' . $schema_name . '_Schema';

		if ( ! class_exists( $schema_class ) ) {
			$this->logger()->warning( sprintf( 'Sender Class %s does not exist, Do not send data.', $schema_class ) );
			return;
		}

		$schema      = new Sparkasse_Schema();
		$distributor = new Distributor_Energy_Certificate( $schema, $energieausweis, $this->logger() );
		$distributor->send();
	}
}
