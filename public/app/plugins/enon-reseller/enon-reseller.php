<?php
/**
 * Plugin initialization file
 *
 * @package Enon_Reseller
 * @since 1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: ENON - Reseller Plugin
 * Plugin URI:  https://energieausweis-online-erstellen.de
 * Description: Core Plugin for Energieausweis Online site.
 * Version:     1.0.0
 * Author:      Awesome UG
 * Author URI:  https://www.awesome.ug
 * Text Domain: enon
 */

namespace Enon_Reseller;

use Awsm\WP_Wrapper\Plugin\Plugin;
use Enon_Reseller\Loader;

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
function enon_reseller_boot() {
	try {

		( new Plugin() )
			->add_task( Loader::class )
			->boot();
	} catch ( \Exception $exception ) {
		wp_die( esc_attr( $exception->getMessage() ) );
	}
}

enon_reseller_boot();
