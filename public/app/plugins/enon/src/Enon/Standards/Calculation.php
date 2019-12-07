<?php

namespace Enon\Enon\Standards;
/**
 * Class Calculations.
 *
 * @since 1.0.0
 *
 * @package Enon\Enon\Standards
 */
class Calculation extends Standard {
	/**
	 * Get calculation file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Type of Energieausweis (vw/bw)
	 *
	 * @return string The location of the schema file.
	 */
	public function getFile( $type )
	{
		$schema_file = $this->getPath() . '/calculations/' . $type . '.php';
		return  apply_filters( 'wpenon_calculation_file', $schema_file, $this->getKey(), $type );
	}
}
