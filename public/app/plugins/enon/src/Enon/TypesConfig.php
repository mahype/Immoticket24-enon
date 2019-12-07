<?php

namespace Enon\Enon;

/**
 * Class Types.
 *
 * @since 1.0.0
 *
 * @package Enon\Enon
 */
class TypesConfig extends Config {
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
			'vw' =>  __( 'Verbrauchsausweis für Wohngebäude', 'wpenon' ),
			'bw' => __( 'Bedarfsausweis für Wohngebäude', 'wpenon' ),
		);
	}
}
