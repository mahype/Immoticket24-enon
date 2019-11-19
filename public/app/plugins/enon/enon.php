<?php

/**
 * Plugin initialization file
 *
 * @package ENON
 * @since 1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: ENON - Core Plugin (neu)
 * Plugin URI:  https://energieausweis-online-erstellen.de
 * Description: Core Plugin for Energieausweis Online site.
 * Version:     1.0.0
 * Author:      Awesome UG
 * Author URI:  https://www.awesome.ug
 * Text Domain: enon
 */

namespace Enon\Core;

use Awsm\WPWrapper\Plugin\Plugin;
use Enon\Exceptions\Exception;

require dirname( __FILE__ ) . '/vendor/autoload.php';

if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

/**
 * Booting Enon Plugin.
 *
 * @since 1.0.0
 */
function enon_boot() {
	try {
		( new Plugin() )
			->addTask( \Enon\Config\Gutenberg::class )
			->addTask( \Enon\Config\Menu::class )
			->addTask( \Enon\Whitelabel\Loader::class )
			->addTask( \Enon\Misc\RemoveOptimizepress::class )
			->addTask( \Enon\Misc\GoogleTagManager::class )
			->boot();
	} catch ( \Exception $exception ) {
		wp_die( $exception->getMessage() );
	}
}

enon_boot();
