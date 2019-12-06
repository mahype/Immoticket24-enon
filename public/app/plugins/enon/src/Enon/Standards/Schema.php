<?php

namespace Enon\Enon\Standards;

/**
 * Class Schema.
 *
 * @since 1.0.0
 *
 * @package Enon\Enon\Standards
 */
class Schema extends Standard {
	/**
	 * Get schema file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Type of Energieausweis (vw/bw)
	 *
	 * @return string The location of the schema file.
	 */
	public function getFile( $type )
	{
		$schema_file = $this->getStandardPath() . '/schema/' . $type . '.php';
		return  apply_filters( 'wpenon_schema_file', $schema_file, $this->getKey(), $type );
	}
}
