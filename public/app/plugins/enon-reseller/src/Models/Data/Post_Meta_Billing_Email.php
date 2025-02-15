<?php
/**
 * Class for getting resellers post meta company data.
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
 * Class Post_Meta_Billing_Email.
 *
 * @since 1.0.0
 */
class Post_Meta_Billing_Email extends Post_Meta {
	/**
	 * Get sender name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Name of the billig email sender.
	 */
	public function get_sender_name() {
		return $this->get( 'bill_sender_name' );
	}

	/**
	 * Get sender email.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email of the billig email sender.
	 */
	public function get_sender_email() {
		return $this->get( 'bill_sender_email' );
	}

	/**
	 * Get subject.
	 *
	 * @since 1.0.0
	 *
	 * @return string Subject for billiug email.
	 */
	public function get_subject() {
		return $this->get( 'bill_subject' );
	}

	/**
	 * Get content.
	 *
	 * @since 1.0.0
	 *
	 * @return string Content for billiug email.
	 */
	public function get_content() {
		return $this->get( 'bill_content' );
	}
}
