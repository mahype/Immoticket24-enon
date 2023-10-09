<?php

/**
 * Class Extension_Form_A
 *
 * @since 1.0.0
 */
class Extension_Form_A extends Extension {
	/**
	 * Length of s1
	 *
	 * @var float
	 *
	 * @since 1.0.0
	 */
	private $length_s1;

	/**
	 * Length of t
	 *
	 * @var float
	 *
	 * @since 1.0.0
	 */
	private $length_t;

	/**
	 * Length of b
	 *
	 * @var float
	 *
	 * @since 1.0.0
	 */
	private $length_b;

	/**
	 * Validating data.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	protected function validate_data() {
		if( empty( $height ) || empty( $s1 ) || empty( $t )) {
			return false;
		}

		return true;
	}

	/**
	 * Setting walls length.
	 *
	 * @param float $length_s1 Length of s1
	 * @param float $length_t  Length of t
	 *
	 * @since 1.0.0
	 */
	public function set_walls( float $length_s1, float $length_t, float $length_b ) {
		$this->length_s1 = $length_s1;
		$this->length_t  = $length_t;
		$this->length_b  = $length_b;
	}

	/**
	 * Get all surface areas at once.
	 *
	 * @return array Surface areas.
	 *
	 * @since 1.0.0
	 */
	public function get_surface_areas() {
		$areas = array(
			's1' => $this->get_surface_area_s1(),
			's2' => $this->get_surface_area_s2(),
			't'  => $this->get_surface_area_t(),
			'b'  => $this->get_surface_area_b(),
		);

		return $areas;
	}

	/**
	 * Get surface area of t.
	 *
	 * @return float Surface area value.
	 *
	 * @since 1.0.0
	 */
	public function get_surface_area_t() {
		return $this->get_length_t() * ( $this->height + 0.5 );
	}

	/**
	 * Get surface area of b.
	 *
	 * @return float Surface area value.
	 *
	 * @since 1.0.0
	 */
	public function get_surface_area_b() {
		return $this->get_length_b() * ( $this->height + 0.5 );
	}

	/**
	 * Get surface area of s1.
	 *
	 * @return float Surface area value.
	 *
	 * @since 1.0.0
	 */
	public function get_surface_area_s1() {
		return ( $this->get_length_t() - $this->get_length_s1() ) * ( $this->height + 0.5 );
	}

	/**
	 * Get surface area of s2.
	 *
	 * @return float Surface area value.
	 *
	 * @since 1.0.0
	 */
	public function get_surface_area_s2() {
		return $this->get_length_s2() * ( $this->height + 0.5 );
	}

	/**
	 * Get floor space.
	 *
	 * @return float Floor space.
	 *
	 * @since 1.0.0
	 */
	public function get_floor_space() {
		return $this->get_length_t() * $this->get_length_b();
	}

	/**
	 * Get surface area.
	 *
	 * @return float Surface area.
	 *
	 * @since 1.0.0
	 */
	public function get_surface_area() {
		return $this->get_surface_area_b() * 2 + $this->get_surface_area_t() + $this->get_surface_area_s2();
	}

	/**
	 * Get length of t.
	 *
	 * @return float Length of t.
	 *
	 * @since 1.0.0
	 */
	public function get_length_t() {
		return $this->length_t;
	}

	/**
	 * Get length of b.
	 *
	 * @return float Length of b.
	 *
	 * @since 1.0.0
	 */
	public function get_length_b() {
		return $this->length_b;
	}

	/**
	 * Get length of s1.
	 *
	 * @return float Length of s1.
	 *
	 * @since 1.0.0
	 */
	public function get_length_s1() {
		return $this->length_s1;
	}

	/**
	 * Get length of s2.
	 *
	 * @return float
	 *
	 * @since 1.0.0
	 */
	public function get_length_s2() {
		return $this->length_b; // Cause it's the opposite wall
	}
}
