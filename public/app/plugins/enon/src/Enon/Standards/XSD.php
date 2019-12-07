<?php

namespace Enon\Enon\Standards;

/**
 * Class Mapping.
 *
 * @since 1.0.0
 *
 * @package Enon\Enon\Standards
 */
class XSD extends Standard {
	/**
	 * Get Mapping file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Type of mapping (pdf/xml-datenerfassung/xml-zusatzdatenerfassung)
	 *
	 * @return string The location of the mapping file.
	 */
	public function getFile( $type )
	{
		$mapping_file = $this->getPath() . '/datenerfassung/' . $type . '.xsd';
		return  apply_filters( 'wpenon_xsd_file', $mapping_file, $this->getKey(), $type );
	}
}
