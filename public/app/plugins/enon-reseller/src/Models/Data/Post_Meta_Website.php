<?php
/**
 * Class for getting resellers post meta website data.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Data
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Data;

use Enon\WP\Models\Post_Meta;

/**
 * Class Post_Meta_Website.
 *
 * @since 1.0.0
 */
class Post_Meta_Website extends Post_Meta {
	/**
	 * Get Website Name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Sitename of current token.
	 */
	public function get_website_name() {
		return $this->get( 'website_name' );
	}

	/**
	 * Get customer edit url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected for editing entry.
	 */
	public function get_customer_edit_url() {
		return trim( $this->get( 'customer_edit_url' ) );
	}

	/**
	 * Get customer edit vw url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected for editing entry.
	 */
	public function get_customer_edit_vw_url() {
		return trim( $this->get( 'customer_edit_vw_url' ) );
	}

	/**
	 * Get customer edit bw url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected for editing entry.
	 */
	public function get_customer_edit_bw_url() {
		return trim( $this->get( 'customer_edit_bw_url' ) );
	}

	/**
	 * Get payment successful url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected after succesful payment.
	 */
	public function get_payment_successful_url() {
		return trim( $this->get( 'payment_successful_url' ) );
	}

	/**
	 * Get payment pending url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected after pending payment.
	 */
	public function get_payment_pending_url() {
		return trim( $this->get( 'payment_pending_url' ) );
	}

	/**
	 * Get payment failed url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected after failed payment.
	 */
	public function get_payment_failed_url() {
		return trim( $this->get( 'payment_failed_url' ) );
	}

	/**
	 * Redirect customer via JS.
	 * 
	 * @return bool True on redirect via JS, false if not.
	 * 
	 * @since 1.0.0
	 */
	public function redirect_via_js() {
		return $this->get( 'redirect_via_js' ) === 'yes' ? true : false ;
	}

	/**
	 * Get privacy url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Privacy url.
	 */
	public function get_privacy_url() {
		return trim( $this->get( 'privacy_url' ) );
	}
}
