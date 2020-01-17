<?php
/**
 * Class for getting confirmation email data.
 *
 * @category Class
 * @package  Enon\WP\Model
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Models;

use Enon\ACF\Models\ACF_Settings;

/**
 * Class Settings.
 *
 * @since 1.0.0
 */
class Options_Confirmation_Email extends Options {
	/**
	 * Get confirmation sender name.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_sender_name() {
		return $this->get( 'confirmation_sender_name' );
	}

	/**
	 * Get confirmation sender email.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_sender_email() {
		return $this->get( 'confirmation_sender_email' );
	}

	/**
	 * Get confirmation subject.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_subject() {
		return $this->get( 'confirmation_subject' );
	}

	/**
	 * Get confirmation content.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_content() {
		return $this->get( 'confirmation_content' );
	}
}
