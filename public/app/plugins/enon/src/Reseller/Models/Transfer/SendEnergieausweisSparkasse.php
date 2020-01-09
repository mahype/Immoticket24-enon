<?php

namespace Enon\Reseller\Models\Transfer;

/**
 * Class SendEnergieausweisSparkasse
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Send
 */
class SendEnergieausweisSparkasse extends SendEnergieausweis {
	/**
	 * Get body to send to endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	protected function getBody() {
		$fields = $this->energieausweis->getSchema()->getFields();

		$data = array();

		foreach ( $fields as  $key => $field ) {
			$data[ $key ] = $this->energieausweis->$key;
		}

		return $data;
	}
}
