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
	public function getToken() : string
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
	public function setToken( $token )
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
		return sanitize_text_field( wp_unslash( $_REQUEST['iframe_token'] ) );
	}
}
