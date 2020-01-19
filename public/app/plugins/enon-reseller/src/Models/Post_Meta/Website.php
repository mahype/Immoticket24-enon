<?php
/**
 * Class for getting resellers post meta website data.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Post_Meta
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Post_Meta;

use Enon\Acf\Models\Post_Meta;

/**
 * Class Settings.
 *
 * @since 1.0.0
 */
class Reseller_Website extends Post_Meta {
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
	 * Get payment failed url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected after failed payment.
	 */
	public function get_payment_failed_url() {
		return trim( $this->get( 'payment_failed_url' ) );
	}
}
