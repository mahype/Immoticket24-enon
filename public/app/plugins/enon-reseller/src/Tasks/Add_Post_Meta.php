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

namespace Enon_Reseller\Tasks;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;

use Awsm\WP_Wrapper\Tools\Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

use Enon\Models\Plugins\ACF;
use Enon\Models\WP\Tabs;
use Enon\Models\WP\Tab;
use Enon\Models\WP\Post_Meta;

use Enon\Models\Fieldsets\Fieldset_Billing_Email;
use Enon\Models\Fieldsets\Fieldset_Confirmation_Email;

use Enon_Reseller\Models\Fieldsets\Fieldset_General;
use Enon_Reseller\Models\Fieldsets\Fieldset_Schema;
use Enon_Reseller\Models\Fieldsets\Fieldset_Iframe;
use Enon_Reseller\Models\Fieldsets\Fieldset_Send_Data;
use Enon_Reseller\Models\Fieldsets\Fieldset_Website;

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
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
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
			->add( new Tab( 'company', __( 'Allgemein', 'enon' ), new Fieldset_General() ) )
			->add( new Tab( 'confirmation_email', __( 'Bestätigungs-Email', 'enon' ), new Fieldset_Confirmation_Email() ) )
			->add( new Tab( 'billing_email', __( 'Rechnungs-Email', 'enon' ), new Fieldset_Billing_Email() ) )
			->add( new Tab( 'schema', __( 'Schema', 'enon' ), new Fieldset_Schema() ) )
			->add( new Tab( 'iframe', __( 'Iframe', 'enon' ), new Fieldset_Iframe() ) )
			->add( new Tab( 'send_data', __( 'Senden', 'enon' ), new Fieldset_Send_Data() ) )
			->add( new Tab( 'website', __( 'Webseite', 'enon' ), new Fieldset_Website() ) );

		return $tabs;
	}
}
