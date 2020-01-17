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

namespace Enon;

use Awsm\WP_Wrapper\Plugin\Plugin;

use Enon\WP\Loader as WP_Loader;
use Enon\CLI\Loader as CLI_Loader;
use Enon\Misc\Loader as Misc_Loader;
use Enon\ACF\Loader as ACF_Loader;

use Enon\Reseller\Loader as Reseller_Loader;

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
			->add_task( WP_Loader::class, $logger )
			->add_task( CLI_Loader::class, $logger )
			->add_task( Misc_Loader::class, $logger )
			->add_task( Reseller_Loader::class, $logger )
			->add_task( ACF_Loader::class, $logger )
			->boot();
	} catch ( \Exception $exception ) {
		wp_die( esc_attr( $exception->getMessage() ) );
	}
}

enon_boot();
