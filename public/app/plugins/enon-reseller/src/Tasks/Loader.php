<?php
/**
 * Reseller loader.
 *
 * @category Class
 * @package  Enon_Reseller
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks;

use Enon\Task_Loader;

use Enon_Reseller\Models\Token;
use Enon_Reseller\Tasks\Enon\Task_Reseller;
use Enon_Reseller\Tasks\Enon\Task_Route_Urls;
use Enon_Reseller\Models\Exceptions\Exception;

use Enon_Reseller\Models\Reseller;
use Enon_Reseller\Models\Reseller_Data;

use Enon_Reseller\Tasks\WP\Add_CPT_Reseller;

use Enon_Reseller\Tasks\WP\Load_Frontend;
use Enon_Reseller\Tasks\Enon\Route_Urls;

use Enon_Reseller\Tasks\Enon\Task_Enon;

use Enon_Reseller\Tasks\Enon\Setup_Enon;
use Enon_Reseller\Tasks\Plugins\Setup_Affiliate_WP;
use Enon_Reseller\Tasks\Plugins\Setup_Edd;

use Enon_Reseller\Tasks\Enon\Filter_Confirmation_Email;
use Enon_Reseller\Tasks\Enon\Filter_Bill_Email;
use Enon_Reseller\Tasks\Enon\Submit_Energieausweis;

use Enon_Reseller\Tasks\Acf\Add_Post_Meta;

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
		if ( is_admin() ) {
			$this->add_admin_tasks();
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
	public function add_admin_tasks() {
		try {
			$reseller_data = new Reseller_Data();
			$reseller      = new Reseller( $reseller_data, $this->logger() );
		} catch ( Exception $exception ) {
			$this->logger()->error( sprintf( $exception->getMessage() ) );
		}

		$this->add_task( Add_CPT_Reseller::class );
		$this->add_task( Add_Post_Meta::class, $this->logger() );
		$this->add_task( Setup_Enon::class, $reseller, $this->logger() );
		$this->add_task( Submit_Energieausweis::class, $reseller, $this->logger() );
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

		$this->add_task( Add_CPT_Reseller::class );

		$this->add_task( Load_Frontend::class, $reseller );
		$this->add_task( Route_Urls::class, $reseller, $this->logger() );

		$this->add_task( Setup_Enon::class, $reseller, $this->logger() );
		$this->add_task( Setup_Affiliate_WP::class, $reseller, $this->logger() );
		$this->add_task( Setup_Edd::class, $reseller, $this->logger() );

		$this->add_task( Filter_Confirmation_Email::class, $reseller, $this->logger() );
		$this->add_task( Filter_Bill_Email::class, $reseller, $this->logger() );

		$this->add_task( Submit_Energieausweis::class, $reseller, $this->logger() );
	}
}

