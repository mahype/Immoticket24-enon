<?php
/**
 * Plugin initialization file
 *
 * @package ENON
 * @since 1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: ENON - Core Plugin
 * Plugin URI:  https://energieausweis-online-erstellen.de
 * Description: Core Plugin for Energieausweis Online site.
 * Version:     1.0.0
 * Author:      Awesome UG
 * Author URI:  https://www.awesome.ug
 * Text Domain: enon
 */

defined( 'ABSPATH' ) || exit;

/**
 * The main function to return the Enon instance.
 *
 * Any extension can use this function to access the main plugin object or to simply check
 * whether the plugin is active and running. Example:
 *
 * `if ( function_exists( 'enon' ) && enon() ) {
 *     // Do custom extension stuff.
 * }
 *
 * @since 1.0.0
 *
 * @return Enon|null The Enon instance, or null on failure.
 */
function enon() {
	if ( ! class_exists( 'Enon' ) ) {
		$main_file        = __FILE__;

		$mu_plugin_dir = wp_normalize_path( WPMU_PLUGIN_DIR );

		require_once( $mu_plugin_dir . '/plugin-lib/plugin-loader.php' );

		if ( ! class_exists( 'Leaves_And_Love_Plugin_Loader' ) ) {
			$locations = array(
				plugin_dir_path( $main_file ) . 'vendor/felixarntz/plugin-lib/plugin-loader.php',
				$mu_plugin_dir . '/plugin-lib/plugin-loader.php',
			);
			foreach ( $locations as $location ) {
				if ( file_exists( $location ) ) {
					require_once $location;
					break;
				}
			}
		}

		require_once plugin_dir_path( $main_file ) . 'src/enon.php';

		Leaves_And_Love_Plugin_Loader::load( 'Enon', $main_file, '' );
	}

	$enon = Leaves_And_Love_Plugin_Loader::get( 'Enon' );
	if ( is_wp_error( $enon ) ) {
		return null;
	}

	return $enon;
}

/**
 * Executes a callback after Torro Forms has been initialized.
 *
 * This function should be used by all Torro Forms extensions to initialize themselves.
 *
 * This doc block was added in the 1500th commit :)
 *
 * @since 1.0.0
 *
 * @param callable $callback Callback to bootstrap the extension.
 */
function enon_load( $callback ) {
	if ( did_action( 'enon_loaded' ) || doing_action( 'enon_loaded' ) ) {
		call_user_func( $callback, enon() );
		return;
	}

	add_action( 'enon_loaded', $callback, 10, 1 );
}

enon();
