<?php

namespace Enon\Whitelabel\WordPress\Plugins;

/**
 * Class ACFPostFields
 *
 * @since 1.0.0
 *
 * @package Enon\Whitelabel\WordPress\Plugins
 */
abstract class ACFPostFields {
	/**
	 * Post Id.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $postId = null;

	/**
	 * Set post id.
	 *
	 * @since 1.0.0
	 *
	 * @param int $postId
	 */
	public function setPostId( $postId )
	{
		$this->postId = $postId;
	}

	/**
	 * Get post field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $field_name Name of the field.
	 *
	 * @return mixed/null Value if found, otherwhise null.
	 */
	public function get( $field_name ) {
		if ( empty ( $this->postId ) ) {
			return null;
		}

		get_field( $field_name, $this->postId );
	}
}
