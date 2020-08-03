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

namespace Enon_Reseller\Tasks\Filters;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Tools\Logger_Trait;

use Enon_Reseller\Models\Reseller;

/**
 * Class EnonEmailOrderConfirmation.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\WordPress
 */
class Filter_General implements Task, Filters, Actions {
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
		$this->add_actions();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'wpenon_bill_to_address', array( $this, 'filter_to_address' ) );
		add_filter( 'wpenon_get_option', array( $this, 'filter_price' ), 10, 2 );
		add_filter( 'wpenon_get_option', array( $this, 'filter_price' ), 10, 2 );
		add_filter( 'wpenon_custom_fees', array( $this, 'filter_custom_fees' ), 100, 1 );
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
	 * @param string $value Value to filter.
	 * @param string $name  Name of value.
	 *
	 * @return string Filtered value.
	 *
	 * @since 1.0.0
	 */
	public function filter_price( $value, $name ) {
		switch ( $name ) {
			case 'bw_download_price':
				$reseller_value = $this->reseller->data()->general->get_price_bw();
				break;
			case 'vw_download_price':
				$reseller_value = $this->reseller->data()->general->get_price_vw();
				break;
			default:
				$reseller_value = '';
				break;
		}

		if ( empty( $reseller_value ) && 0 !== $reseller_value ) {
			return $value;
		}

		return $reseller_value;
	}

	/**
	 * Filtering custom fees
	 *
	 * @param array $fees Fees.
	 *
	 * @return array Filtered fees.
	 *
	 * @since 1.0.0
	 */
	public function filter_custom_fees( $fees ) {
		$custom_fees = $this->reseller->data()->general->get_custom_fees();

		if ( null === $custom_fees ) {
			return $fees;
		}

		foreach ( $fees as $index => $fee ) {
			if ( 'eingabesupport' === $fee['id'] ) {
				continue;
			}

			if ( ! is_array( $custom_fees ) || ! in_array( $fee['id'], $custom_fees ) ) {
				unset( $fees[ $index ] );
			}
		}

		return $fees;
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

		$affiliate_id = $this->reseller->data()->general->get_affiliate_id();

		if ( empty( $affiliate_id ) ) {
			return;
		}

		affiliate_wp()->tracking->referral = $affiliate_id;
		affiliate_wp()->tracking->set_affiliate_id( $affiliate_id );
	}
}
