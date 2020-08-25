<?php
/*
Plugin Name: WP Energieausweis online
Plugin URI: http://leaves-webdesign.com
Description: Dieses Plugin erlaubt Besuchern der Website, gültige Energieausweise gegen Bezahlung online zu erstellen.
Version: 1.0.2
Author: Felix Arntz
Author URI: http://leaves-webdesign.com
License: Private
Text Domain: wpenon
Domain Path: /inc/languages/
Tags: wordpress, plugin, app, energieausweis, online
*/
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

define( 'WPENON_VERSION', '1.0.2' );
define( 'WPENON_REQUIRED_PHP', '5.4.0' );
define( 'WPENON_REQUIRED_WP', '4.0' );
define( 'WPENON_REQUIRED_EDD', '2.2.0' );

define( 'WPENON_REQUIRED_MEMORY_LIMIT', 256 ); // FPDF requires a lot of memory

define( 'WPENON_NAME', 'WP Energieausweis Online' );
define( 'WPENON_MAINFILE', __FILE__ );
define( 'WPENON_BASENAME', plugin_basename( WPENON_MAINFILE ) );
define( 'WPENON_PATH', untrailingslashit( plugin_dir_path( WPENON_MAINFILE ) ) );
define( 'WPENON_URL', untrailingslashit( plugin_dir_url( WPENON_MAINFILE ) ) );
define( 'WPENON_DATA_PATH', WP_CONTENT_DIR . '/wpenon' );
define( 'WPENON_DATA_URL', WP_CONTENT_URL . '/wpenon' );

require_once WPENON_PATH . '/inc/functions.php';

define( 'WPENON_VERSION_CHECK', wpenon_version_check() );

if ( ! defined( 'EDD_SLUG' ) ) {
	define( 'EDD_SLUG', 'energieausweise' );
}
if ( ! defined( 'EDD_DISABLE_ARCHIVE' ) ) {
	define( 'EDD_DISABLE_ARCHIVE', true );
}

function wpenon_init() {
	load_plugin_textdomain( 'wpenon', false, dirname( WPENON_BASENAME ) . '/inc/languages/' );

	if ( ! function_exists( 'enon_boot' ) ) {
	// 	return;
	}

	if ( WPENON_VERSION_CHECK === true ) {
		if ( wpenon_memory_limit_check() ) {
			require_once WPENON_PATH . '/inc/dynamic-functions.php';
			require_once WPENON_PATH . '/inc/logger-trait.php';
			require_once WPENON_PATH . '/vendor/autoload.php';
			\WPENON\App::instance();
		} else {
			add_action( 'admin_notices', 'wpenon_display_memory_limit_error_notice' );
		}
	} else {
		add_action( 'admin_notices', 'wpenon_display_version_error_notice' );
	}
}

add_filter( 'request', function($query){
	if( array_key_exists( 'wpenon_thumbnail_upload', $_POST ) && empty($_POST['wpenon_thumbnail_upload']) && filter_var($_POST['wpenon_thumbnail_upload'], FILTER_VALIDATE_BOOLEAN) && empty((int) $_FILES['wpenon_thumbnail_file']['error'])) {
		$frontend = \WPENON\Controller\Frontend::instance();
		$frontend->_handleRequest();
	}elseif(array_key_exists( 'wpenon_thumbnail_delete', $_POST ) && !empty($_POST['wpenon_thumbnail_delete']) && array_key_exists( 'energieausweis_id', $_POST ) && !empty($_POST['energieausweis_id']) && filter_var($_POST['wpenon_thumbnail_delete'], FILTER_VALIDATE_BOOLEAN)) {
		$imageId = get_post_meta( $_POST['energieausweis_id'], '_thumbnail_id', true );
		delete_post_thumbnail( $_POST['energieausweis_id'] );
		wp_delete_attachment($imageId);
		die(json_encode([
			                'tmpImage' => [
				                'deleted' => true
			                ]
		                ])
		);
	}

	return $query;
}, 1);

add_action( 'plugins_loaded', 'wpenon_init' );

register_activation_hook( WPENON_MAINFILE, array( 'WPENON\App', 'install' ) );
//register_uninstall_hook( WPENON_MAINFILE, array( 'WPENON\App', 'uninstall' ) );
