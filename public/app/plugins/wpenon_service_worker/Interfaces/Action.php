<?php # -*- coding: utf-8 -*-

namespace WPENON\ServiceWorker\Interfaces;

interface Action extends Service{

	/**
	 * Initialize a service worker that use add_action as start hook
	 * @param array $args - arguments for the ne service
	 *
	 * @return mixed
	 */
	public function initAction();
}
