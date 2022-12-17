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
use Enon\Logger;

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
		add_filter( 'wpenon_filter_url',              array( $this, 'add_iframe_params' ) );
		add_filter( 'wpenon_payment_success_url',     array( $this, 'add_iframe_params' ), 10 );	
		add_filter( 'awsm_edd_payment_success_url',     array( $this, 'add_iframe_params' ), 10 );	
		
		add_filter( 'wpenon_payment_failed_url',      array( $this, 'filter_payment_failed_url' ) );
		add_filter( 'wpenon_overview_page_data',      array( $this, 'filter_access_link' ), 10, 2 );
		add_filter( 'wpenon_create_privacy_url',      array( $this, 'filter_privacy_url' ) );

		add_filter( 'edd_get_success_page_uri',       array( $this, 'filter_success_url' ), 100 );
		add_filter( 'edd_get_success_page_uri',       array( $this, 'add_iframe_params' ), 200 );	
		add_filter( 'edd_get_checkout_uri',           array( $this, 'add_iframe_params' ), 100 );
		add_filter( 'edd_get_failed_transaction_uri', array( $this, 'add_iframe_params' ), 100 );
		add_filter( 'edd_remove_fee_url',             array( $this, 'add_iframe_params' ), 100 );		
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
	public function add_iframe_params( $url ) {
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
		$url = $this->reseller->data()->website->get_payment_successful_url();

		if( $this->reseller->data()->website->redirect_via_js() && ! empty($url) ) {
			echo '<html lang="en">
			<head>
				<meta charset="UTF-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>Document</title>
			</head>
			<body>				
				<script>
					var message = JSON.stringify( {"redirect_url": "' . $url . '"} );
					console.log(message);
					parent.postMessage( message, "*" );
				</script>
			</body>
			</html>';
			exit;
		}

		// Backup to standard value.
		if ( empty( $url ) ) {
			$url = home_url( '/danke-fuer-ihr-vertrauen/' );
		}

		$this->logger->notice( 'Setting success url.', [ 'url' => $url ] );

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

		$this->logger->notice('Redirecting user after failed payment.', array( 'url' => $url ) );

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
		switch ( $energy_certificate->type ) {
			case 'vw':
				$url = $this->reseller->data()->website->get_customer_edit_vw_url();
				break;
			case 'bw':
				$url = $this->reseller->data()->website->get_customer_edit_bw_url();
				break;
			default:
				return $data;
		}

		if ( empty( $url ) ) {
			return $data;
		}

		$url = $this->reseller->add_iframe_params( $url, $energy_certificate->id );

		$data['access_link'] = $url;

		$this->logger->notice( 'Filtering access link.', array( 'url' => $url ) );

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

		$this->logger->notice('Filtering privacy url.', array( 'url' => $privacy_url ) );

		return $privacy_url;
	}
}
