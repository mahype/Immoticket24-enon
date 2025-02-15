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
	 * Get company id.
	 *
	 * @since 1.0.0
	 *
	 * @return string Id of the company.
	 */
	public function get_company_id() {
		return $this->get( 'company_id' );
	}

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
	 * Checks if title element is checked.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if is checked..
	 */
	public function isset_marketing_klicktipp() {
		$values = $this->get( 'marketing' );

		if ( ! empty( $values ) && in_array( 'klicktipp', $values ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if email have to be redirected to reseller.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if email have to be sent to reseller, false if not.
	 */
	public function redirect_bill_to_reseller() {
		$email_settings = $this->get( 'email_settings' );

		if( is_array( $email_settings ) && in_array( 'redirect_bill_to_reseller', $email_settings ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if order confirmation have to be sent to reseller.
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool True if order confirmation have to be sent to reseller, false if not.
	 */
	public function send_order_confirmation_to_reseller() {
		$email_settings = $this->get( 'email_settings' );

		if( is_array( $email_settings ) && in_array( 'send_order_confirmation_to_reseller', $email_settings ) ) {
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
	 * Get custom fees.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_custom_fees() {
		return $this->get( 'custom_fees' );
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
