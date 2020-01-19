<?php
/**
 * Class for loading Affiliate WP functionality for resellers.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Plugins
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Plugins;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;


use Awsm\WP_Wrapper\Traits\Logger as Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

use Enon\Models\Exceptions\Exception;
use Enon_Reseller\Models\Reseller;

/**
 * Class Task_Affiliate_WP.
 *
 * @since 1.0.0
 */
class Setup_Affiliate_WP implements Task, Actions {

	use Logger_Trait;

	/**
	 * Reseller object.
	 *
	 * @since 1.1.0
	 *
	 * @var Reseller
	 */
	private $reseller;

	/**
	 * AffiliateWP constructor.
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
	 * Running scripts.
	 *
	 * @since 1.0.0
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
		add_action( 'template_redirect', array( $this, 'set_affiliatewp_referal' ), -10000, 0 );
	}

	/**
	 * Adjusting referal.
	 *
	 * @since 1.0.0
	 */
	public function set_affiliatewp_referal() {
		if ( ! self::is_activated() ) {
			$this->logger->alert( 'Affiliate WP seems not to be activated.' );
			return;
		}

		$affiliate_id = $this->reseller->data()->get_affiliate_id();

		if ( empty( $affiliate_id ) ) {
			return;
		}

		affiliate_wp()->tracking->referral = $affiliate_id;
		affiliate_wp()->tracking->set_affiliate_id( $affiliate_id );
	}

	/**
	 * Is activated.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Is Affiliate WP activated.
	 */
	public static function is_activated() {
		if ( ! function_exists( 'affiliate_wp' ) ) {
			return false;
		}

		return true;
	}
}
