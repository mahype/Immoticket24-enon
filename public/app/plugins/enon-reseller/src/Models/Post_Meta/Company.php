<?php
/**
 * Class for getting resellers post meta company data.
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
class Company extends Post_Meta {
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
