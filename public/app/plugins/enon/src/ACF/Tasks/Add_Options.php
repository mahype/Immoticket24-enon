<?php
/**
 * Loading ACF Options.
 *
 * @category Class
 * @package  Enon\ACF
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Acf\Tasks;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;

use Enon\Task_Loader;
use Enon\Traits\Logger as Logger_Trait;
use Enon\Logger;

use Enon\Acf\Models\ACF;
use Enon\Acf\Models\Page;
use Enon\Acf\Models\Tabs;
use Enon\Acf\Models\Tab;
use Enon\Acf\Models\Fieldset;

use Enon\Acf\Fieldsets\Fieldset_Billing_Email;
use Enon\Acf\Fieldsets\Fieldset_Confirmation_Email;

/**
 * Class Add_Options.
 *
 * @package Enon\Config
 */
class Add_Options implements Task, Actions {
	use Logger_Trait;

	/**
	 * AffiliateWP constructor.
	 *
	 * @param Logger $logger Logger object.
	 * @since 1.0.0
	 */
	public function __construct( Logger $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		if ( ! ACF::is_activated() ) {
			$this->logger->warning( 'Advanced custom fields seems not to be activated.' );
			return;
		}

		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'acf/init', [ $this, 'setup_option_pages' ] );
	}

	/**
	 * Setup option pages.
	 *
	 * @since 1.0.0
	 */
	public function setup_option_pages() {
		$mail_page = new Page( 'mail_settings', __( 'Mails', 'enon' ), __( 'Mails', 'enon' ) );

		$mail_page
			->add_tabs( $this->get_tabs() )
			->register();
	}

	/**
	 * Get tabs.
	 *
	 * @since 1.0.0
	 */
	private function get_tabs() {
		$tabs = new Tabs();

		$tabs
			->add( new Tab( 'confirmation_email', __( 'Bestätigung', 'enon' ), new Fieldset_Confirmation_Email() ) )
			->add( new Tab( 'bill_email', __( 'Rechnung', 'enon' ), new Fieldset_Billing_Email() ) );

		return $tabs;
	}
}
