<?php # -*- coding: utf-8 -*-

namespace WPENON\ServiceWorker\Interfaces;

interface Service {

	/**
	 * @param string $name
	 *
	 * @return void
	 */
	public function setServiceName(string $name);

	/**
	 * @return string
	 */
	public function getServiceName();
}
