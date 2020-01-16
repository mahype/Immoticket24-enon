<?php
/**
 * Reseller loader.
 *
 * @category Class
 * @package  Enon\Reseller
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Reseller;

use Enon\Reseller\Models\Token;
use Enon\Reseller\Tasks\Enon\Task_Reseller;
use Enon\Reseller\Tasks\Enon\Task_Route_Urls;
use Enon\Task_Loader;
use Enon\Models\Exceptions\Exception;

use Enon\Reseller\Models\Reseller;
use Enon\Reseller\Models\Reseller_Data;

use Enon\Reseller\Tasks\Core\Task_CPT_Reseller;
use Enon\Reseller\Tasks\Core\Task_Frontend;
use Enon\Reseller\Tasks\Enon\Task_Email_Confirmation;
use Enon\Reseller\Tasks\Enon\Task_Email_Order_Confirmation;
use Enon\Reseller\Tasks\Enon\Task_Enon;
use Enon\Reseller\Tasks\Enon\Task_Send_Energieausweis;
use Enon\Reseller\Tasks\Plugins\Task_ACF;
use Enon\Reseller\Tasks\Plugins\Task_Affiliate_WP;
use Enon\Reseller\Tasks\Plugins\Task_Edd;

/**
 * Whitelabel loader.
 *
 * @package Enon\Config
 */
class Loader extends Task_Loader {
	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_task( Task_CPT_Reseller::class );
		$this->add_task( Task_ACF::class, $this->logger() );

		if ( is_admin() ) {
			$this->addAdminTasks();
		} else {
			$this->addFrontendTasks();
		}

		$this->run_tasks();
	}

	/**
	 * Running admin tasks.
	 *
	 * @since 1.0.0
	 */
	public function addAdminTasks() {
		try {
			$reseller_data = new Reseller_Data();
			$reseller = new Reseller( $reseller_data, $this->logger() );
		} catch ( Exception $exception ) {
			$this->logger()->error( sprintf( $exception->getMessage() ) );
		}

		$this->add_task( Task_Reseller::class, $reseller, $this->logger() );
		$this->add_task( Task_Send_Energieausweis::class, $reseller, $this->logger() );
	}

	/**
	 * Running frontend tasks.
	 *
	 * @since 1.0.0
	 */
	public function addFrontendTasks() {
		$token = new Token();

		// No token, no action.
		if ( empty( $token->get() ) ) {
			return;
		}

		try {
			$reseller_data = new Reseller_Data( $token );
			$reseller = new Reseller( $reseller_data, $this->logger() );
		} catch ( Exception $exception ) {
			$this->logger()->error( sprintf( $exception->getMessage() ) );
		}

		$this->add_task( Task_Frontend::class, $reseller );
		$this->add_task( Task_Reseller::class, $reseller, $this->logger() );
		$this->add_task( Task_Route_Urls::class, $reseller, $this->logger() );
		$this->add_task( Task_Email_Confirmation::class, $reseller, $this->logger() );
		$this->add_task( Task_Email_Order_Confirmation::class, $reseller, $this->logger() );
		$this->add_task( Task_Send_Energieausweis::class, $reseller, $this->logger() );
		$this->add_task( Task_Affiliate_WP::class, $reseller, $this->logger() );
		$this->add_task( Task_Edd::class, $reseller, $this->logger() );
	}
}

