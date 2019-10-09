<?php

namespace awsmug\Enon\Modules\Legal;

use awsmug\Enon\Modules\Hooks_Submodule_Interface;
use awsmug\Enon\Modules\Hooks_Submodule_Trait;
use awsmug\Enon\Modules\Performance\Performance;

/**
 * Class Optimize Press
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Cookie_Consent extends Performance implements Hooks_Submodule_Interface {
	use Hooks_Submodule_Trait;

	/**
	 * Bootstraps the submodule by setting properties.
	 *
	 * @since 1.0.0
	 */
	protected function bootstrap() {
		$this->slug  = 'cookie-consent';
		$this->title = __( 'Cookie Consent', 'enon' );
	}

	/**
	 * Sets up all action and filter hooks for the service.
	 *
	 * @since 1.0.0
	 */
	protected function setup_hooks() {
		parent::setup_hooks();

		if ( is_admin() ) {
			return;
		}

		$this->actions[] = array(
			'name' => 'wp_enqueue_scripts',
			'callback' => array( $this, 'remove_scripts' ),
			'priority' => 10000,
		);
	}
}
