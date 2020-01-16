<?php
/**
 * Task which loads reseller email sctripts to system.
 *
 * @category Class
 * @package  Enon\Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Reseller\Tasks\Enon;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Enon\Logger;
use Enon\Traits\Logger as Logger_Trait;
use Enon\Reseller\Models\Reseller;
use Enon\Models\Edd\Edd_Payment;
use WPENON\Model\Energieausweis;

/**
 * Class Send_Energieausweis
 *
 * @since 1.0.0
 */
class Task_Send_Energieausweis implements Actions, Task {
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
		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'edd_update_payment_status', array( $this, 'sendData' ), 10, 2 );
	}

	/**
	 * Send data.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $payment_id Payment id.
	 * @param string $status Payment status.
	 */
	public function sendData( $payment_id, $status ) {
		$endpoint = $this->reseller->data()->get_post_endpoint();

		// Do not anything if not payed or if there is no endpoint given.
		if ( 'publish' !== $status || empty( $endpoint ) ) {
			return;
		}

		$sender_class_name = 'Enon\\Reseller\\Models\\Transfer\\' . $this->reseller->data()->get_post_data_config_class();

		if ( ! class_exists( $sender_class_name ) ) {
			$this->logger()->warning( sprintf( 'Sender Class %s does not exist, Do not send data.', $sender_class_name ) );
			return;
		}

		$energieausweis_id = ( new Edd_Payment( $payment_id ) )->get_energieausweis_id();
		$energieausweis    = new Energieausweis( $energieausweis_id );

		( new $sender_class_name( $endpoint, $energieausweis, $this->logger() ) )->send();
	}
}
