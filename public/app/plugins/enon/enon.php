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
	$logger = new \Monolog\Logger();

	\Awsm\WP_Plugin\PluginFactory::create()
		->add_service( \Enon\Config\Gutenberg::class )
		->add_service( \Enon\Config\Menu::class )
		->add_service( \Enon\Misc\Remove_Optimizepress::class )
		->add_service( \Enon\Misc\Google_Tag_Manager::class )
		->add_service( \Enon\Whitelabel\WhitelabelLoader::class, $logger )
		->boot();
}

enon_boot();

(new Plugin)
	->set_name( 'Enon' )
	->set_version( '1.0.0' )
	->set_translation( new Translation( 'enon', dirname( __FILE__ ) . '/languages'  ) )
	->set_activation( new Activation )
	->set_deactivation( new Deactivation )
	->add_service( new Servicename( $logger ) )
	->boot();
