<?php # -*- coding: utf-8 -*-

namespace WPENON\ServiceWorker\Interfaces;

interface Filter {

	/**
	 * Initialize a service worker that use add_filter as start hook
	 * @param array $args - arguments for the ne service
	 *
	 * @return mixed
	 */
	public function initFilter(array $args);
}
