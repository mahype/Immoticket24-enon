<?php
/**
 * Plugin initialization file
 *
 * @package Enon
 * @since 1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: ENON - Core Plugin (neu)
 * Plugin URI:  https://energieausweis.de
 * Description: Core Plugin for Energieausweis Online site.
 * Version:     1.0.0
 * Author:      Awesome UG
 * Author URI:  https://www.awesome.ug
 * Text Domain: enon
 */

namespace Enon;

use Awsm\WP_Wrapper\Plugin\Plugin;

use Enon\WP\Loader as WP_Loader;
use Enon\Tasks\Loader as Tasks_Loader;

if ( ! defined( 'WPINC' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

if( is_admin() ) {
	set_time_limit(120);
}

/**
 * Booting Enon Plugin.
 *
 * @since 1.0.0
 */
function enon_boot() {
	try {

		( new Plugin() )
			->add_task( WP_Loader::class )
			->add_task( Tasks_Loader::class )
			->boot();
	} catch ( \Exception $exception ) {
		wp_die( esc_attr( $exception->getMessage() ) );
	}
}

enon_boot();

function enon_translations( $translation, $text, $domain ) {
	$translations = array(
		'affiliate-wp' => array(
			'Name' => 'Name',
		)
	);

	if ( ! in_array( $domain, array_keys( $translations ) ) ) {
		return $translation;
	}

	if ( ! in_array( $text, array_values( $translations[ $domain ] ) ) ) {
		return $translation;
	}

	return array_search( $text, $translations[ $domain ] );
}

add_filter( 'gettext', '\Enon\enon_translations', 10, 3 );