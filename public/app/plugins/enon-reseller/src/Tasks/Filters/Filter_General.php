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
use Enon\Logger;

use Enon_Reseller\Models\Reseller;

/**
 * Class EnonEmailOrderConfirmation.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\WordPress
 */
class Filter_General implements Task, Filters, Actions {

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
		add_filter( 'wpenon_bill_to_address', array( $this, 'filter_bill_to_address' ) );
		add_filter( 'wpenon_get_option', array( $this, 'filter_price' ), 10, 2 );
		add_filter( 'wpenon_get_option', array( $this, 'filter_price' ), 10, 2 );
		add_filter( 'wpenon_custom_fees', array( $this, 'filter_custom_fees' ), 100, 1 );
		add_filter( 'eddkti_add_customer', array( $this, 'filter_send_customer_to_klicktipp' ), 100, 1 );
		add_filter( 'affwp_tracking_is_valid_affiliate', array( $this, 'filter_affiliatewp_tracking_is_valid_affiliate' ), 100, 2 );
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'template_redirect', array( $this, 'set_affiliatewp_referal' ), -10000, 0 );
		add_action( 'wpenon_energieausweis_create', array( $this, 'update_reseller_id' ) );

		add_action( 'edd_admin_sale_notice', array( $this, 'add_order_email_filter' ), 9, 2 );
		add_action( 'edd_admin_sale_notice', array( $this, 'remove_order_email_filter' ), 11, 2 );
	}


	public function add_order_email_filter() {
		add_filter( 'edd_admin_notice_emails', array( $this, 'filter_order_confirmation_to_address' ), 100 );
	}

	public function remove_order_email_filter() {
		remove_filter( 'edd_admin_notice_emails', array( $this, 'filter_order_confirmation_to_address' ), 100 );
	}

	/**
	 * Filter to email adress of order confirmation.
	 *
	 * @param string $email To email address.
	 *
	 * @return string Filtered to email address.
	 *
	 * @since 1.0.0
	 */
	public function filter_order_confirmation_to_address( $emails ) {
		$reseller_contact_email = $this->reseller->data()->general->get_contact_email();
		array_push( $emails, 'reseller@immoticket24.de' );

		if ( ! $this->reseller->data()->general->send_order_confirmation_to_reseller() ) {
			return $emails;
		}

		array_push( $emails, $reseller_contact_email );

		return $emails;
	}

	/**
	 * Updating reseller id.
	 *
	 * @since 1.0.0
	 *
	 * @param Energieausweis $energieausweis Energieausweis object.
	 */
	public function update_reseller_id( $energieausweis ) {
		update_post_meta( $energieausweis->id, 'reseller_id', $this->reseller->get_id() );
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
	public function filter_bill_to_address( $email ) {
		$reseller_contact_email = $this->reseller->data()->general->get_contact_email();

		if ( ! $this->reseller->data()->general->redirect_bill_to_reseller() || empty( $reseller_contact_email ) ) {
			return $email;
		}

		return $reseller_contact_email;
	}



	/**
	 * Filter if customer can be send to klicktipp.
	 *
	 * @param bool $can_send True if user can be send to klicktipp, false if not.
	 *
	 * @return bool $can_send True if user can be send to klicktipp, false if not.
	 *
	 * @since 1.0.0
	 */
	public function filter_send_customer_to_klicktipp( $can_send ) {
		if ( ! $this->reseller->data()->general->isset_marketing_klicktipp() ) {
			return false;
		}

		return $can_send;
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

	/**
	 * Filter is valid affiliate. Enable tracking for reseller purchases himself.
	 *
	 * @param bool $valid True if affiliate is valid, false if not.
	 * @param int  $affiliate_id Affiliate id.
	 * @return bool True if affiliate is valid, false if not.
	 */
	public function filter_affiliatewp_tracking_is_valid_affiliate( $valid, $affiliate_id ) {
		if ( empty( $affiliate_id ) ) {
			$affiliate_id = affiliate_wp()->get_affiliate( $affiliate_id );
		}
		$affiliate = affwp_get_affiliate( $affiliate_id );
		if ( $affiliate ) {
			$is_self = is_user_logged_in() && get_current_user_id() == $affiliate->user_id;
			$active  = 'active' === $affiliate->status;
		}
		if ( $is_self && $active ) {
			$valid = true;
		}
		return $valid;
	}
}
