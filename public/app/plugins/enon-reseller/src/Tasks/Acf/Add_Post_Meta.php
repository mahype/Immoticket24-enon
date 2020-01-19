<?php
/**
 * Loading ACF postmeta fields.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Acf
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Acf;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;

use Enon\Task_Loader;
use Awsm\WP_Wrapper\Traits\Logger as Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

use Enon\Acf\Models\ACF;
use Enon\Acf\Models\Page;
use Enon\Acf\Models\Tabs;
use Enon\Acf\Models\Tab;
use Enon\Acf\Models\Post_Meta;
use Enon\Acf\Models\Fieldset;

use Enon\Acf\Fieldsets\Fieldset_Billing_Email;
use Enon\Acf\Fieldsets\Fieldset_Confirmation_Email;

use Enon_Reseller\Models\Acf\Fieldsets\Fieldset_Reseller_Company;
use Enon_Reseller\Models\Acf\Fieldsets\Fieldset_Reseller_Form;
use Enon_Reseller\Models\Acf\Fieldsets\Fieldset_Reseller_Iframe;
use Enon_Reseller\Models\Acf\Fieldsets\Fieldset_Reseller_Send_Data;
use Enon_Reseller\Models\Acf\Fieldsets\Fieldset_Reseller_Website;

/**
 * Class Add_Options.
 *
 * @package Enon\Config
 */
class Add_Post_Meta implements Task, Actions {
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
		add_action( 'acf/init', [ $this, 'setup_post_meta_fields' ] );
	}

	/**
	 * Setup option pages.
	 *
	 * @since 1.0.0
	 */
	public function setup_post_meta_fields() {
		$mail_page = new Post_Meta( 'reseller' );

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
			->add( new Tab( 'company', __( 'Firma', 'enon' ), new Fieldset_Reseller_Company() ) )
			->add( new Tab( 'confirmation_email', __( 'Bestätigung', 'enon' ), new Fieldset_Confirmation_Email() ) )
			->add( new Tab( 'billing_email', __( 'Rechnung', 'enon' ), new Fieldset_Billing_Email() ) )
			->add( new Tab( 'form', __( 'Formular', 'enon' ), new Fieldset_Reseller_Form() ) )
			->add( new Tab( 'iframe', __( 'Iframe', 'enon' ), new Fieldset_Reseller_Iframe() ) )
			->add( new Tab( 'send_data', __( 'Senden', 'enon' ), new Fieldset_Reseller_Send_Data() ) )
			->add( new Tab( 'website', __( 'Webseite', 'enon' ), new Fieldset_Reseller_Website() ) );

		return $tabs;
	}
}
