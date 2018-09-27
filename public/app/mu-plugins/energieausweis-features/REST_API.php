<?php
/**
 * REST API URL adjustment.
 */

namespace Immoticket24\Energieausweis_Features;

class REST_API {

	public function run() {
		add_filter( 'rest_url_prefix', array( $this, 'get_url_prefix' ) );
		add_filter( 'subdirectory_reserved_names', array( $this, 'adjust_url_prefix_reserved_directories' ) );
	}

	public function get_url_prefix() {
		return 'api';
	}

	public function adjust_url_prefix_reserved_directories( $names ) {
		$names[] = 'api';

		return array_diff( $names, array( 'wp-json' ) );
	}
}
