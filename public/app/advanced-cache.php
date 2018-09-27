<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

define( 'WP_ROCKET_ADVANCED_CACHE', true );
$rocket_cache_path = '/www/htdocs/w012900a/energieausweis-online-erstellen.de/app/cache/wp-rocket/';
$rocket_config_path = '/www/htdocs/w012900a/energieausweis-online-erstellen.de/app/wp-rocket-config/';

if ( file_exists( '/www/htdocs/w012900a/energieausweis-online-erstellen.de/app/plugins/wp-rocket/inc/vendors/classes/class-rocket-mobile-detect.php' ) && ! class_exists( 'Rocket_Mobile_Detect' ) ) {
	include_once '/www/htdocs/w012900a/energieausweis-online-erstellen.de/app/plugins/wp-rocket/inc/vendors/classes/class-rocket-mobile-detect.php';
}
if ( file_exists( '/www/htdocs/w012900a/energieausweis-online-erstellen.de/app/plugins/wp-rocket/inc/front/process.php' ) ) {
	include '/www/htdocs/w012900a/energieausweis-online-erstellen.de/app/plugins/wp-rocket/inc/front/process.php';
} else {
	define( 'WP_ROCKET_ADVANCED_CACHE_PROBLEM', true );
}