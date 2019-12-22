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
	 * Energieausweis id.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $id;

	/**
	 * Type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Standard.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $standard;

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

		$this->type     = get_post_meta( $this->id, 'wpenon_type', true );
		$this->standard = get_post_meta( $this->id, 'wpenon_standard', true );
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
	 * Get energieausweis type.
	 *
	 * @since 1.0.0
	 *
	 * @return string Energieausweis type.
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get energieausweis standard.
	 *
	 * @since 1.0.0
	 *
	 * @return string Energieausweis standard.
	 */
	public function getStandard()
	{
		return $this->standard;
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
