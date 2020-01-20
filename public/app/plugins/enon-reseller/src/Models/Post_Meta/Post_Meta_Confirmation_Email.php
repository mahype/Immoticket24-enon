<?php
/**
 * Class for getting resellers post meta confirmation email data.
 *
 * @category Class
 * @package  Enon\WP\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Post_Meta;

use Enon\WP\Models\Post_Meta;

/**
 * Class Post_Meta_Confirmation_Email.
 *
 * @since 1.0.0
 */
class Post_Meta_Confirmation_Email extends Post_Meta {
	/**
	 * Get sender name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Sender name.
	 */
	public function get_sender_name() {
		return $this->get( 'confirmation_sender_name' );
	}

	/**
	 * Get sender email.
	 *
	 * @since 1.0.0
	 *
	 * @return string Sender email.
	 */
	public function get_sender_email() {
		return $this->get( 'confirmation_sender_email' );
	}

	/**
	 * Get subject.
	 *
	 * @since 1.0.0
	 *
	 * @return string Subject.
	 */
	public function get_subject() {
		return $this->get( 'confirmation_subject' );
	}

	/**
	 * Get content.
	 *
	 * @since 1.0.0
	 *
	 * @return string Content.
	 */
	public function get_content() {
		return $this->get( 'confirmation_content' );
	}
}
