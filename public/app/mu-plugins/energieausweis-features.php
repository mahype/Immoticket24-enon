<?php
/*
Plugin Name: Energieausweis-Features
Plugin URI: https://energieausweis-online-erstellen.de
Description: WordPress-Anpassungen und Features für energieausweis-online-erstellen.de.
Version: 1.0.0
Author: Felix Arntz
Author URI: https://felix-arntz.me
*/

namespace Immoticket24\Energieausweis_Features;

function load_features() {
	$features = array(
		'Branded_Login',
		'Cleaner',
		'REST_API',
	);

	foreach ( $features as $feature_class ) {
		require_once plugin_dir_path( __FILE__ ) . 'energieausweis-features/' . $feature_class . '.php';

		$classname = __NAMESPACE__ . '\\' . $feature_class;
		$instance  = new $classname();
		$instance->run();
	}
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_features' );
