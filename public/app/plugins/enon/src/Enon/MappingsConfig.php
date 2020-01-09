<?php

namespace Enon\Enon;

use Enon\Enon\Standards\Schema;

/**
 * Class for managing Standards.
 *
 * @package Enon\Enon
 *
 * @todo Renaming standards?
 */
class MappingsConfig extends Config {

	/**
	 * Initiating types.
	 *
	 * @since 1.0.0
	 *
	 * @todo Loading dynamically.
	 */
	protected function initiate() {
		 $this->configData = array(
			 'pdf' => __( 'PDF', 'wpenon' ),
			 'xml-datenerfassung' => __( 'XML Datenerfassung', 'wpenon' ),
			 'xml-zusatzdatenerfassung' => __( 'XML Zusatzdatenerfassung', 'wpenon' ),
		 );
	}
}
