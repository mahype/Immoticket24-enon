<?php

namespace Enev\Schema;

/**
 * Class Schema
 *
 * @since 1.0.0
 */
class Schema {
	/**
	 * Insert a value or key/value pair after a specific key in an array.
	 *
	 * @param array $array Array to change.
	 * @param string $section Section name.
	 * @param string $key Key after value have to be added.
	 * @param array $data Array to add.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	protected function insert_after_key( array $array, string $section_name, string $key, array $data ) {
		$section = $array['groups'][ $section_name ]['fields'];

		$keys    = array_keys( $section );
		$index   = array_search( $key, $keys );
		$pos     = false === $index ? count( $section ) : $index + 1;
		$section = array_merge( array_slice( $section, 0, $pos ), $data, array_slice( $section, $pos ) );

		$array['groups'][ $section_name ]['fields'] = $section;

		return $array;
	}
}
