<?php
/**
 * Class for getting billing email data.
 *
 * @category Class
 * @package  Enon\WP\Model
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Models;

/**
 * Class Settings.
 *
 * @since 1.0.0
 */
class Options_Billing_Email extends Options {
	/**
	 * Get bill sender name.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_sender_name() {
		return $this->get( 'bill_sender_name' );
	}

	/**
	 * Get bill sender email.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_sender_email() {
		return $this->get( 'bill_sender_email' );
	}

	/**
	 * Get bill subject.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_subject() {
		return $this->get( 'bill_subject' );
	}

	/**
	 * Get bill content.
	 *
	 * @from
	 *
	 * @since 1.0.0
	 */
	public function get_content() {
		return $this->get( 'bill_content' );
	}
}
