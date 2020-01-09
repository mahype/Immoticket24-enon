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

use Awsm\WP_Wrapper\Plugin\Plugin;
use Enon\Config\Loader as ConfigLoader;
use Enon\Misc\Loader as MiscLoader;
use Enon\Reseller\Loader as ResellerLoader;
use Enon\Logger;

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
		$logger = new Logger( 'Enon' );

		( new Plugin() )
			->add_task( ConfigLoader::class, $logger )
			->add_task( MiscLoader::class, $logger )
			->add_task( ResellerLoader::class, $logger )
			->boot();
	} catch ( \Exception $exception ) {
		wp_die( $exception->getMessage() );
	}
}

enon_boot();
