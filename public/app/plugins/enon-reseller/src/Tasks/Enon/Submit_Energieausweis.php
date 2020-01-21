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
use Awsm\WP_Wrapper\Traits\Logger as Logger_Trait;

use Enon_Reseller\Models\Reseller;
use Enon_Reseller\Models\Reseller_Data;
use Enon\Edd\Models\Payment;

use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Submit_Energieausweis
 *
 * @since 1.0.0
 */
class Submit_Energieausweis implements Actions, Task {
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
		$energieausweis_old = new Energieausweis_Old( $energieausweis_id );
		$reseller_id        = $energieausweis_old->reseller_id;

		if ( empty( $reseller_id ) ) {
			return;
		}

		$reseller_data = new Reseller_Data( $reseller_id );
		$post_endpoint = $reseller_data->send_data->get_post_endpoint();

		if ( empty( $post_endpoint ) ) {
			return;
		}

		if( ! $this->has_relevant_values( $energieausweis ) ) {
			return;
		}

		$sender_class_name = 'Enon_Reseller\\Models\\Requests\\' . $reseller_data->send_data->get_post_data_config_class();

		if ( ! class_exists( $sender_class_name ) ) {
			$this->logger()->warning( sprintf( 'Sender Class %s does not exist, Do not send data.', $sender_class_name ) );
			return;
		}

		$request = new $sender_class_name( $post_endpoint, $energieausweis_old, $this->logger() );
		$request->post();
	}

	/**
	 * Checks if thera is any relevand data for relevant.
	 *
	 * @param Energieausweis_Old $energieausweis Energieausweis object.
	 *
	 * @return bool True if it has relevant values.
	 *
	 * @since 1.0.0
	 */
	private function has_relevant_values( $energieausweis ) {
		$values_to_check = [
			'mauerwerk'
		];


		return true;
	}
}
