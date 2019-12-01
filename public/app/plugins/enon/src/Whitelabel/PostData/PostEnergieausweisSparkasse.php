<?php

namespace Enon\Whitelabel\PostData;

/**
 * Class PostSparkasseEnergieausweis
 *
 * @since 1.0.0
 *
 * @package Enon\Whitelabel\Send
 */
class PostEnergieausweisSparkasse extends PostEnergieausweis {
	/**
	 * Get body to send to endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	protected function getBody()
	{
		$data = $this->energieausweis;
		return $data;
	}
}
