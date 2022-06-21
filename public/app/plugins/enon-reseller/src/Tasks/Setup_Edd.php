<?php

/**
 * Task which loads reseller email sctripts to system.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Tools\Logger_Trait;

use Enon_Reseller\Models\Reseller;
use Enon\Models\Edd\Payment;

use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Submit_Energieausweis
 *
 * @since 1.0.0
 */
class Setup_Edd implements Actions, Task
{
	use Logger_Trait;

	/**
	 * Constructor.
	 * 
	 * @param Logger Logger object.
	 * 
	 * @since 1.0.0
	 */
	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Running task.
	 *
	 * @since 1.0.0
	 
	 * @return mixed|void
	 */
	public function run()
	{
		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions()
	{
		add_action('edd_update_payment_status', array($this, 'finish_after_payment'), 10, 2);
	}

	/**
	 * Send data after a completed payment.
	 *
	 * @param int    $payment_id Payment id.
	 * @param string $status     Status of payment.
	 *
	 * @since 1.0.0
	 */
	public function finish_after_payment(int $payment_id, string $status)
	{
		if ('publish' !== $status) {
			return;
		}

		$payment = new Payment($payment_id);

		$energieausweis_id = $payment->get_energieausweis_id();
		$energieausweis    = new Energieausweis_Old($energieausweis_id);
		$reseller_id       = $energieausweis->reseller_id;

		if (empty($reseller_id)) {
			return;
		}

		$reseller = new Reseller($reseller_id);

		$this->send_data($energieausweis, $reseller, $payment_id);

		$affiliate_id = $reseller->data()->general->get_affiliate_id();
		if (!empty($affiliate_id)) {
			affiliate_wp()->tracking->referral = $affiliate_id;
			affiliate_wp()->tracking->set_affiliate_id($affiliate_id);
		}
	}

	/**
	 * Send data.
	 *
	 * @param Energieausweis_Old $energieausweis Energieausweis object.
	 * @param int                $reseller_id    Reseller id.
	 *
	 * @since 1.0.0
	 */
	public function send_data(Energieausweis_Old $energieausweis, Reseller $reseller, $payment_id )
	{
		$sender_name  = ucfirst($reseller->data()->general->get_company_id());
		$sender_class = 'Enon_Reseller\\Models\\Api\\Out\\' . $sender_name;

		// Is there an sender name which was set? Bail out if not.
		if (!class_exists($sender_class)) {
			$sender_class = 'Enon_Reseller\\Models\\Api\\Out\\Standard';
		}

		$sender = new $sender_class($this->logger(), $energieausweis, $reseller, $payment_id);
		$sender->send();
	}
}
