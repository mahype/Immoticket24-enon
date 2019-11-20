<?php

namespace Enon\Whitelabel\WordPress;

use Awsm\WPWrapper\BuildingPlans\Task;
use Enon\Traits\Logger as LoggerTrait;

/**
 * Class WordPress.
 *
 * Running WordPress scripts
 *
 * @package Enon\Whitelabel\WordPress
 */
class Core implements Task {
	use LoggerTrait;

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		add_filter( 'template_include', array( $this, 'filter_iframe_template' ) );
		add_action( 'wp_head', 'wp_no_robots' );

		remove_action( 'wp_footer', 'immoticketenergieausweis_trusted_shops_badge_script', 100 );
	}

	/**
	 * Filtering iframe template.
	 *
	 * @since 1.0.0
	 *
	 * @return string $template The path of the template to include.
	 */
	public function filter_iframe_template() {
		return locate_template( array( 'energieausweis-iframe.php' ) );
	}
}
