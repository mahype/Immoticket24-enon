<?php

namespace Enon\Whitelabel;

use Enon\Exceptions\Exception;

/**
 * Class Token.
 *
 * @package Enon\Whitelabel
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
	 * @return string
	 */
	public function get() : string
	{
		if ( empty( $this->token ) ) {
			$this->token = $this->getByRequest();
		}

		return $this->token;
	}

	/**
	 * Set token.
	 *
	 * @since 1.0.0
	 *
	 * @param $token
	 */
	public function set( $token )
	{
		$this->token = $token;
	}

	/**
	 * Checks if current request is whitelabeled.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token
	 */
	public function getByRequest() : string
	{
		if( ! isset ( $_REQUEST['iframe_token'] ) ) {
			return false;
		}

		return sanitize_text_field( wp_unslash( $_REQUEST['iframe_token'] ) );
	}
}
