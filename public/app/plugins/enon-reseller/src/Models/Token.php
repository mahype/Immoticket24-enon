<?php
/**
 * Token object.
 *
 * @category Class
 * @package  Enon_Reseller\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models;

/**
 * Class Token.
 *
 * @package Enon_Reseller
 */
class Token {
	/**
	 * Token.
	 *
	 * @since 1.0.0
	 *
	 * @var string;
	 */
	private $token = false;

	/**
	 * Get token.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string.
	 */
	public function get() : string {
		if ( empty( $this->token ) ) {
			$this->token = $this->get_by_request();
		}

		return $this->token;
	}

	/**
	 * Set token.
	 *
	 * @since 1.0.0
	 *
	 * @param string $token Token string.
	 */
	public function set( $token ) {
		$this->token = $token;
	}

	/**
	 * Checks if current request is whitelabeled.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token
	 */
	public function get_by_request() : string {
		// phpcs:ignore
		if ( ! isset( $_REQUEST['iframe_token'] ) ) {
			return false;
		}

		return sanitize_text_field( wp_unslash( $_REQUEST['iframe_token'] ) );
	}
}
