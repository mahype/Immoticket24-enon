<?php

namespace Enon\Whitelabel\Plugins;

use Awsm\WPWrapper\BuildingPlans\Task;

/**
 * Class Wpenon.
 *
 * Running WordPress scripts
 *
 * @package Enon\Whitelabel
 */
class Wpenon implements Task {
	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		add_filter( 'wpenon_payment_success_url', array( $this, 'filterPaymentSuccess_url' ) );
		add_filter( 'wpenon_payment_failed_url', array( $this, 'filterPaymentFailed_url' ) );
	}
}
