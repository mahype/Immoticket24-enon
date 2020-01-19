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

namespace Enon\Models\Post_Meta;

use Enon\Acf\Models\Post_Meta;

/**
 * Class Settings.
 *
 * @since 1.0.0
 */
class Confirmation_Email extends Post_Meta {
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
