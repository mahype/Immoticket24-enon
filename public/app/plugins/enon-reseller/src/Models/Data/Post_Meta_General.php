<?php
/**
 * Class for getting resellers post meta general data.
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
 * Class Post_Meta_General.
 *
 * @since 1.0.0
 */
class Post_Meta_General extends Post_Meta {
	/**
	 * Get company name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function get_company_name() {
		return $this->get( 'company_name' );
	}

	/**
	 * Get contact name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function get_contact_name() {
		return $this->get( 'contact_name' );
	}

	/**
	 * Get contact email.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function get_contact_email() {
		return $this->get( 'contact_email' );
	}

	/**
	 * Get send to reseller.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if email have to be sent to reseller.
	 */
	public function isset_send_bill_to_reseller() {
		$send_bill_to_reseller = $this->get( 'send_bill_to_reseller' );

		if ( ! empty( $send_bill_to_reseller ) && in_array( 'send_bill_to_reseller', $send_bill_to_reseller ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get price for bedarfsausweis.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_price_bw() {
		return $this->get( 'price_bw' );
	}

	/**
	 * Get price for verbrauchsausweis.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_price_vw() {
		return $this->get( 'price_vw' );
	}

	/**
	 * Get afiliate id.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_affiliate_id() {
		return $this->get( 'affiliate_id' );
	}

	/**
	 * Get token.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function get_token() {
		return $this->get( 'token' );
	}
}
