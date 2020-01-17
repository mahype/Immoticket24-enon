<?php
/**
 * Task which loads email confirmation scripts.
 *
 * @category Class
 * @package  Enon\Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Reseller\Tasks\Enon;

use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Enon\Logger;
use Enon\Traits\Logger as Logger_Trait;
use Enon\Reseller\Models\Reseller;

/**
 * Class EnonEmailConfirmation.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Task_Email_Confirmation implements Task, Filters {

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
		add_filter( 'wpenon_confirmation_sender_email', array( $this, 'filter_from_address' ) );
		add_filter( 'wpenon_confirmation_sender_name', array( $this, 'filter_from_name' ) );
		add_filter( 'wpenon_confirmation_link', array( $this, 'filter_link' ), 10, 2 );
		add_filter( 'wpenon_confirmation_site', array( $this, 'filtersite_name' ), 10, 1 );

		add_filter( 'wpenon_email_legal', array( $this, 'filter_legal' ) );
		add_filter( 'wpenon_alternative_email_footer', array( $this, 'filter_alternative_footer' ) );
		add_filter( 'wpenon_email_signature', array( $this, 'filter_signature' ), 20 );
	}

	/**
	 * Returning token from email address.
	 *
	 * @param string $email Email from address to filter.
	 *
	 * @return string $email Reseller from email address.
	 *
	 * @since 1.0.0
	 */
	public function filter_from_address( $email ) {
		$reseller_mail = $this->reseller->data()->get_email_sender_address();

		if ( empty( $reseller_mail ) ) {
			return $email;
		}

		return $reseller_mail;
	}

	/**
	 * Returning token from email name.
	 *
	 * @param string $name Name of the sender.
	 *
	 * @return string $name Reseller sender name if given or global sender name.
	 *
	 * @since 1.0.0
	 */
	public function filter_from_name( $name ) {
		$reseller_name = $this->reseller->data()->get_email_sender_name();

		if ( empty( $reseller_name ) ) {
			return $name;
		}

		return $reseller_name;
	}

	/**
	 * Set site name to signature.
	 *
	 * $since 1.0.0
	 *
	 * @param string $site_name Site name of sender.
	 *
	 * @return string Filtered signature.
	 */
	public function filtersite_name( $site_name ) {
		$resellersite_name = $this->reseller->data()->get_website_name();

		if ( empty( $resellersite_name ) ) {
			return $site_name;
		}

		return $resellersite_name;
	}

	/**
	 * Set site name to signature.
	 *
	 * @param string         $link           Link to site.
	 * @param Energieausweis $energieausweis Energieausweis object.
	 *
	 * @return string Filtered signature.
	 *
	 * @since 1.0.0
	 */
	public function filter_link( $link, $energieausweis ) {
		$customer_edit_url = $this->reseller->data()->get_customer_edit_url();

		if ( empty( $customer_edit_url ) ) {
			return $link;
		}

		$redirect_url = $this->reseller->create_verfied_url( $customer_edit_url, $energieausweis->id );

		if ( false === $redirect_url ) {
			return $link;
		}

		return $redirect_url;
	}

	/**
	 * Set email footer.
	 *
	 * @param string $footer Footer HTML.
	 *
	 * @return string $footer Filtered footer HTML.
	 *
	 * @since 1.0.0
	 */
	public function filter_alternative_footer( $footer ) {
		$reseller_footer = $this->reseller->data()->get_email_footer();

		if ( empty( $reseller_footer ) ) {
			return $footer;
		}

		$footer  = '<div style="font-size:14px;">';
		$footer .= wpautop( $reseller_footer );
		$footer .= '</div>';
		// translators: Confirmation email to customer was sent.
		$footer .= '<small>' . sprintf( __( 'Diese Email wurde automatisch von <a href="%1$s">%2$s</a> versendet.', 'wpenon' ), $this->reseller->data()->get_customer_edit_url(), $this->reseller->data()->get_website_name() ) . '</small>';

		return $footer;
	}

	/**
	 * Set legal.
	 *
	 * @param string $legal Legal text.
	 *
	 * @return string Filtered footer.
	 *
	 * @since 1.0.0
	 */
	public function filter_legal( $legal ) {
		$resellercustomer_edit_url = $this->reseller->data()->get_customer_edit_url();
		$reseller_website_name = $this->reseller->data()->get_website_name();

		if ( empty( $resellercustomer_edit_url ) ) {
			return $legal;
		}

		if ( empty( $reseller_website_name ) ) {
			$parsed_url = parse_url( $resellercustomer_edit_url );

			if ( ! $parsed_url ) {
				$reseller_website_name = $resellercustomer_edit_url;
			} else {
				$reseller_website_name = $parsed_url['host'];
			}
		}

		// translators: Legal text.
		return sprintf( __( 'Diese Email wurde automatisch von <a href="%1$s">%2$s</a> versendet.', 'wpenon' ), $resellercustomer_edit_url, $reseller_website_name );
	}

	/**
	 * Set signature.
	 *
	 * @param string $signature Signature text.
	 *
	 * @return string Filtered signature text.
	 *
	 * @since 1.0.0
	 */
	public function filter_signature( $signature ) {
		$reseller_website_name = $this->reseller->data()->get_website_name();

		if ( empty( $reseller_website_name ) ) {
			return $signature;
		}

		// translators: Signature text.
		$signature = sprintf( __( 'Mit freundlichen Grüßen,

		Ihr Team von %s.', 'wpenon' ), $reseller_website_name );

		return $signature;
	}
}
