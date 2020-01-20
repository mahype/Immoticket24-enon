<?php
/**
 * Task which loads email order confirmation scripts.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Enon;

use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Traits\Logger as Logger_Trait;

use Enon_Reseller\Models\Reseller;

/**
 * Class EnonEmailOrderConfirmation.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\WordPress
 */
class Filter_General implements Task, Filters {
	use Logger_Trait;

	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 * @var Reseller;
	 */
	private $reseller;

	/**
	 * Wpenon constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller Reseller object.
	 * @param Logger   $logger   Logger object.
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
		$this->add_filters();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'wpenon_bill_to_address', array( $this, 'filter_to_address' ) );
		add_filter( 'wpenon_price_bw', array( $this, 'filter_price_bw' ) );
		add_filter( 'wpenon_price_vw', array( $this, 'filter_price_vw' ) );
		add_action( 'template_redirect', array( $this, 'set_affiliatewp_referal' ), -10000, 0 );
	}

	/**
	 * Filter to email adress of bill.
	 *
	 * @param string $email To email address.
	 *
	 * @return string Filtered to email address.
	 *
	 * @since 1.0.0
	 */
	public function filter_to_address( $email ) {
		$reseller_contact_email = $this->reseller->data()->general->get_contact_email();

		if ( ! $this->reseller->data()->general->isset_send_bill_to_reseller() || empty( $reseller_contact_email ) ) {
			return $email;
		}
		return $reseller_contact_email;
	}

	/**
	 * Filter price for bedarfsausweis.
	 *
	 * @param string $price Price.
	 *
	 * @return string Filtered price.
	 *
	 * @since 1.0.0
	 */
	public function filter_price_bw( $price ) {
		$reseller_price = $this->reseller->data()->general->get_price_bw();

		if ( empty( $reseller_price ) ) {
			return $price;
		}

		return $reseller_price;
	}

	/**
	 * Filter price for verbrauchsausweis.
	 *
	 * @param string $price Price.
	 *
	 * @return string Filtered price.
	 *
	 * @since 1.0.0
	 */
	public function filter_price_vw( $price ) {
		$reseller_price = $this->reseller->data()->general->get_price_vw();

		if ( empty( $reseller_price ) ) {
			return $price;
		}

		return $reseller_price;
	}

	/**
	 * Adjusting referal.
	 *
	 * @since 1.0.0
	 */
	public function set_affiliatewp_referal() {
		if ( ! function_exists( 'affiliate_wp' ) ) {
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
}
