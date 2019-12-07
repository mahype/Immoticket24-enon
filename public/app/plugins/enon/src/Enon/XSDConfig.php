<?php

namespace Enon\Enon;

use Enon\Enon\Standards\Schema;

/**
 * Class for managing XSD Config.
 *
 * @package Enon\Enon
 *
 * @todo Renaming standards?
 */
class XSDConfig extends Config
{
	/**
	 * Initiating types.
	 *
	 * @since 1.0.0
	 *
	 * @todo Loading dynamically.
	 */
	protected function initiate()
	{
		$this->configData = array(
			'datenerfassung' => __( 'Datenerfassung', 'wpenon' ),
			'zusatzdatenerfassung' => __( 'Zusatzdatenerfassung', 'wpenon' ),
		);
	}
}
