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

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;

use Awsm\WP_Wrapper\Tools\Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

use Enon\Models\Plugins\ACF;
use Enon\Models\WP\Page;
use Enon\Models\WP\Tabs;
use Enon\Models\WP\Tab;

use Enon\Models\Fieldsets\Fieldset_Billing_Email;
use Enon\Models\Fieldsets\Fieldset_Confirmation_Email;

/**
 * Class Add_Options.
 *
 * @package Enon\Config
 */
class Add_Options_General implements Task, Actions {
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
