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

namespace Enon_Reseller\Tasks\Enon;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tools\Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

use Enon_Reseller\Models\Reseller;

use WPENON\Model\Energieausweis;

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
		add_filter( 'wpenon_payment_success_url', array( $this, 'filter_payment_success_url' ) );
		add_filter( 'wpenon_payment_failed_url', array( $this, 'filter_payment_failed_url' ) );

		add_filter( 'wpenon_create_privacy_url', array( $this, 'filter_privacy_url' ) );

		add_filter( 'edd_get_checkout_uri', array( $this, 'filter_iframe_url' ), 100 );
		add_filter( 'edd_get_success_page_uri', array( $this, 'filter_iframe_url' ), 100 );
		add_filter( 'edd_get_failed_transaction_uri', array( $this, 'filter_iframe_url' ), 100 );
		add_filter( 'edd_remove_fee_url', array( $this, 'filter_iframe_url' ), 100 );
	}

	/**
	 * Filtering iframe URL.
	 *
	 * @param mixed $url Extra query args to add to the URI.
	 *
	 * @return string
	 */
	public function filter_iframe_url( $url ) {
		return $this->reseller->create_iframe_url( $url );
	}

	/**
	 * Filtering payment success URL.
	 *
	 * @param string $old_url Old url.
	 *
	 * @return string
	 */
	public function filter_payment_success_url( $old_url ) {
		$url = $this->reseller->data()->website->get_payment_successful_url();

		if ( empty( $url ) ) {
			$payment_successful_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_successful_payment' );

			if ( empty( $payment_successful_page ) ) {
				return $old_url;
			}

			$url = get_permalink( $payment_successful_page );
		}

		$url = $this->reseller->create_verfied_url( $url );

		return $url;
	}

	/**
	 * Filtering payment failed URL.
	 *
	 * @param string $old_url Old url.
	 *
	 * @return string
	 */
	public function filter_payment_failed_url( $old_url ) {
		$url = $this->reseller->data()->website->get_payment_failed_url();

		if ( empty( $url ) ) {
			$payment_failed_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_failed_payment' );

			if ( empty( $payment_failed_page ) ) {
				return $old_url;
			}

			$url = get_permalink( $payment_failed_page );
		}

		$url = $this->reseller->create_verfied_url( $url );

		return $url;
	}

	/**
	 * Filtering privacy URL.
	 *
	 * @param mixed $url Extra query args to add to the URI.
	 *
	 * @return string
	 */
	public function filter_privacy_url( $url ) {
		$privacy_url = $this->reseller->data()->website->get_privacy_url( $url );

		if ( empty( $privacy_url ) ) {
			return $url;
		}
		return $privacy_url;
	}
}
