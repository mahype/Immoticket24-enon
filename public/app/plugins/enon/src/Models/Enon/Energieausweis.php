<?php

namespace Enon\Models\Enon;

/**
 * Class Energieausweis
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Taks\Plugins
 */
class Energieausweis {
	/**
	 * Payment id.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $id;

	/**
	 * Energieausweis constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id Energieausweis id.
	 */
	public function __construct( $id )
	{
		$this->id = $id;
	}

	/**
	 * Get energieausweis id.
	 *
	 * @since 1.0.0
	 *
	 * @return int Energieausweis id.
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get access token for editing page.
	 *
	 * @since 1.0.0
	 *
	 * @return string $access_token      Token to use in URL.
	 */
	public function getAccessToken() {
		return md5( get_post_meta( $this->id, 'wpenon_email', true ) ) . '-' . get_post_meta( $this->id, 'wpenon_secret', true );
	}
}
