<?php
/**
 * Class for managing ACF Fields.
 *
 * @category Class
 * @package  Enon\Core\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Core\Tasks;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Enon\Models\ACF\ACF;
use Enon\Core\Model\Data_Mail;
use Enon\Traits\Logger as Logger_Trait;
use Enon\Logger;

/**
 * Class Task_Settings_ACF.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Task_Settings_ACF implements Task, Actions {

	use Logger_Trait;

	/**
	 * Fieldsets which need to be registered.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $field_sets;

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
		add_action( 'acf/init', array( $this, 'register_mail_page' ) );
		add_action( 'acf/init', array( $this, 'register_mail_options' ) );
	}

	/**
	 * Adding mail page.
	 *
	 * @since 1.0.0
	 */
	public function register_mail_page() {
		$page = acf_add_options_page(
			array(
				'page_title'  => __( 'Mails', 'enon' ),
				'menu_title'  => __( 'Mails', 'enon' ),
				'menu_slug'   => 'enon-mails',
				'capability'  => 'edit_posts',
				'parent_slug' => 'enon',
				'redirect'    => false,
			)
		);

		acf_add_options_page( $page );
	}

	/**
	 * Adding field group.
	 *
	 * @since 1.0.0
	 */
	public function register_mail_options() {
		$fieldset = ( new Data_Mail() )->fieldset();

		$options = array(
			'key'                   => 'group_email_settings',
			'title'                 => 'Email Einstellungen',
			'fields'                => $fieldset,
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'enon-mails',
					),
				),
			),

		);

		acf_add_local_field_group( $options );
	}
}
