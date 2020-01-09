<?php

namespace Enon\Reseller\Models\Transfer;

/**
 * Class SendEnergieausweisStandard
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Send
 */
class SendEnergieausweisStandard extends SendEnergieausweis {
	/**
	 * Get body to send to endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	protected function getBody() {
		$data = $this->energieausweis;
		return $data;
	}
}
