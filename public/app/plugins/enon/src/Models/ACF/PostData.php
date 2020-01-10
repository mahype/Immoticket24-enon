<?php

namespace Enon\Models\ACF;

/**
 * Class ACFPostFields
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Taks\Plugins
 */
abstract class PostData {
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
	public function set_post_id( $postId ) {
		$this->postId = $postId;
	}

	/**
	 * Get post id.
	 *
	 * @since 1.0.0
	 *
	 * @return int $postId Post Id.
	 */
	public function getPostId() {
		return $this->postId;
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
		if ( empty( $this->postId ) ) {
			return null;
		}

		return get_field( $field_name, $this->postId );
	}
}
