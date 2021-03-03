<?php
/**
 * Task which routes urls for resellers.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Filters;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tools\Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

use Enon_Reseller\Models\Reseller;

use WPENON\Model\Energieausweis AS Energieausweis_Old;

/**
 * Class Wpenon.
 *
 * Running WordPress scripts
 *
 * @package Enon_Reseller\WordPress
 */
class Filter_Website implements Task, Filters {
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
	 * @param Reseller $reseller Reseller object.
	 * @param Logger   $logger   Logger object.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Reseller $reseller, Logger $logger ) {
		$this->reseller = $reseller;
		$this->logger = $logger;
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
		add_filter( 'wpenon_filter_url', array( $this, 'filter_iframe_url' ) );

		add_filter( 'wpenon_payment_success_url', array( $this, 'filter_payment_success_url' ), 10, 2 );
		add_filter( 'wpenon_payment_failed_url', array( $this, 'filter_payment_failed_url' ) );

		add_filter( 'wpenon_overview_page_data', array( $this, 'filter_access_link' ), 10, 2 );
		add_filter( 'wpenon_create_privacy_url', array( $this, 'filter_privacy_url' ) );

		add_filter( 'edd_get_checkout_uri', array( $this, 'filter_iframe_url' ), 100 );
		add_filter( 'edd_get_success_page_uri', array( $this, 'filter_iframe_url' ), 100 );
		add_filter( 'edd_get_success_page_uri', array( $this, 'filter_success_url' ), 200 );
		add_filter( 'edd_get_failed_transaction_uri', array( $this, 'filter_iframe_url' ), 100 );
		add_filter( 'edd_remove_fee_url', array( $this, 'filter_iframe_url' ), 100 );
	}

	/**
	 * Filtering iframe URL.
	 *
	 * @param mixed $url Extra query args to add to the URI.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function filter_iframe_url( $url ) {
		return $this->reseller->add_iframe_params( $url );
	}

	/**
	 * Filter success url (also on no payment).
	 *
	 * @param string $url Url to redeírect after successful checkout.
	 *
	 * @return string Filtered url.
	 *
	 * @since 1.0.0
	 */
	public function filter_success_url( $url ) {
        $success_url = $this->reseller->data()->website->get_payment_successful_url();

		if ( ! empty( $success_url ) ) {
			return $success_url;
        }
        
        $ifame_token = $this->reseller->data()->general->get_token();
        $success_url = get_home_url() . '/danke-fuer-ihr-vertrauen/?iframe_token=' . $ifame_token;

		return $success_url;
	}

	/**
	 * Filtering payment success URL.
	 *
	 * @param string $old_url    Old url.
	 * @param int    $payment_id Payment id.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function filter_payment_success_url( $old_url, $payment_id ) {
		$payment = new \EDD_Payment( $payment_id );

		$payment_status = $payment->status;

		if ( 'pending' === $payment->status ) {
			$url = $this->reseller->data()->website->get_payment_pending_url();
		} else {
			$url = $this->reseller->data()->website->get_payment_successful_url();
		}

		// Backup to standard values.
		if ( empty( $url ) ) {
			// Todo: Have to be an option in new settings pages.
			$payment_successful_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_successful_payment' );

			if ( empty( $payment_successful_page ) ) {
				return $old_url;
			}

			$url = get_permalink( $payment_successful_page );
		}

		$debug_values = array(
			'payment_id'     => $payment_id,
			'url'            => $url,
			'payment_status' => $payment->status,
		);

		$url = $this->reseller->add_iframe_params( $url );

		$this->logger()->notice( 'Setting payment url.', $debug_values );

		return $url;
	}

	/**
	 * Filtering payment failed URL.
	 *
	 * @param string $old_url Old url.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function filter_payment_failed_url( $old_url ) {
		$url = $this->reseller->data()->website->get_payment_failed_url();

		// Backup to standard values.
		if ( empty( $url ) ) {
			// Todo: Have to be an option in new settings pages.
			$payment_failed_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_failed_payment' );

			if ( empty( $payment_failed_page ) ) {
				return $old_url;
			}

			$url = get_permalink( $payment_failed_page );
		}

		$url = $this->reseller->add_iframe_params( $url );

		$this->logger()->notice('Redirecting user after failed payment.', array( 'url' => $url ) );

		return $url;
	}

	/**
	 * Filtering access link
	 *
	 * @param array              $data               Data on page overview request.
	 * @param Energieausweis_Old $energy_certificate Energieausweis object.
	 *
	 * @return array Filtered data.
	 *
	 * @since 1.0.0
	 */
	public function filter_access_link( $data, $energy_certificate ) {
		$url = $this->reseller->data()->website->get_customer_edit_url();

		if ( empty( $url ) ) {
			return $data;
		}

		switch ( $energy_certificate->type ) {
			case 'vw':
				$url .= '/verbrauchsausweis/';
				break;
			case 'bw':
				$url .= '/bedarfsausweis/';
				break;
			default:
				return $data;
		}

		$url = $this->reseller->add_iframe_params( $url, $energy_certificate->id );

		$data['access_link'] = $url;

		$this->logger()->notice( 'Filtering access link.', array( 'url' => $url ) );

		return $data;
	}

	/**
	 * Filtering privacy URL.
	 *
	 * @param mixed $url Extra query args to add to the URI.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function filter_privacy_url( $url ) {
		$privacy_url = $this->reseller->data()->website->get_privacy_url();

		if ( empty( $privacy_url ) ) {
			return $url;
		}

		$this->logger()->notice('Filtering privacy url.', array( 'url' => $privacy_url ) );

		return $privacy_url;
	}
}
