<?php

abstract class Extension {
	/**
	 * Height of extension.
	 *
	 * @var
	 *
	 * @since 1.0.0
	 */
	protected $height;

	/**
	 * Extension constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Validate given data.
	 *
	 * @return mixed
	 *
	 * @since 1.0.0
	 */
	abstract protected function validate_data();

	/**
	 * Get floor space.
	 *
	 * @return mixed
	 *
	 * @since 1.0.0
	 */
	abstract public function get_floor_space();

	/**
	 * Get suface area.
	 *
	 * @return mixed
	 *
	 * @since 1.0.0
	 */
	abstract public function get_surface_area();

	/**
	 * Set extension height.
	 *
	 * @param float $height Height of extension.
	 *
	 * @since 1.0.0
	 */
	public function set_height( float $height ) {
		$this->height = $height;
	}
}
