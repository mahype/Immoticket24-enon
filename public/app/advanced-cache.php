<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

define( 'WP_ROCKET_ADVANCED_CACHE', true );
$rocket_cache_path = '/var/www/html/app/cache/wp-rocket/';
$rocket_config_path = '/var/www/html/app/wp-rocket-config/';

if ( file_exists( '/var/www/html/app/plugins/wp-rocket/inc/vendors/classes/class-rocket-mobile-detect.php' ) && ! class_exists( 'Rocket_Mobile_Detect' ) ) {
	include_once '/var/www/html/app/plugins/wp-rocket/inc/vendors/classes/class-rocket-mobile-detect.php';
}
if ( file_exists( '/var/www/html/app/plugins/wp-rocket/inc/front/process.php' ) ) {
	include '/var/www/html/app/plugins/wp-rocket/inc/front/process.php';
} else {
	define( 'WP_ROCKET_ADVANCED_CACHE_PROBLEM', true );
}