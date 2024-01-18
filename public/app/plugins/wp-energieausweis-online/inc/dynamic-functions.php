<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

function wpenon_ajax_dynamic_callback() {
	if ( ! isset( $_POST['security_nonce'] ) || ! check_ajax_referer( WPENON_AJAX_PREFIX . 'energieausweis', 'security_nonce', false ) ) {
		die( 'error::' . __( 'Unauthorisierte Anfrage.', 'wpenon' ) );
	}

	if ( ! isset( $_POST['callback'] ) || ! isset( $_POST['callback_args'] ) ) {
		die( 'error::' . __( 'Ungültige Anfrage: Callback nicht vollständig angegeben.', 'wpenon' ) );
	}

	if ( 0 !== strpos( $_POST['callback'], 'wpenon_' ) ) {
		die( 'error::' . __( 'Ungültige Anfrage: Die Callback-Funktion muss mit wpenon_ beginnen.', 'wpenon' ) );
	}

	if ( ! is_callable( $_POST['callback'] ) ) {
		die( 'error::' . sprintf( __( 'Ungültige Anfrage: Die Callback-Funktion "%s" existiert nicht.', 'wpenon' ), $_POST['callback'] ) );
	}

	$args = $_POST['callback_args'];

	$output = call_user_func_array( $_POST['callback'], $args );
	if ( is_array( $output ) ) {
		if ( isset( $output['error'] ) ) {
			die( 'error::' . $output['error'] );
		}
		$output = json_encode( $output );
	} elseif ( $output === null ) {
		$output = json_encode( null );
	}
	echo $output;
	die();
}



add_action( 'wp_ajax_wpenon_dynamic_callback', 'wpenon_ajax_dynamic_callback' );
add_action( 'wp_ajax_nopriv_wpenon_dynamic_callback', 'wpenon_ajax_dynamic_callback' );

if ( file_exists( WPENON_DATA_PATH . '/dynamic-functions.php' ) ) {
	require_once WPENON_DATA_PATH . '/dynamic-functions.php';
}

if ( ! function_exists( 'wpenon_show_on_bool_compare_and_is_admin' ) ) {
	function wpenon_show_on_bool_compare_and_is_admin( $value, $required_values, $relation = 'AND' ) {
		if ( current_user_can( 'manage_options' ) && wpenon_show_on_bool_compare( $value, $required_values, $relation ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wpenon_show_on_bool_compare' ) ) {
	function wpenon_show_on_bool_compare( $value, $required_values, $relation = 'AND' ) {
		$value           = \WPENON\Util\Parse::arr( $value );
		$required_values = \WPENON\Util\Parse::arr( $required_values );

		$value           = array_map( '\WPENON\Util\Parse::boolean', $value );
		$required_values = array_map( '\WPENON\Util\Parse::boolean', $required_values );

		$results = array();
		for ( $key = 0; $key < count( $value ); $key++ ) {
			if ( isset( $required_values[ $key ] ) && $value[ $key ] === $required_values[ $key ] ) {
				$results[] = $value[ $key ];
			}
		}

		if ( strtoupper( $relation ) == 'OR' ) {
			if ( count( $results ) > 0 ) {
				return true;
			}

			return false;
		} else {
			if ( count( $results ) == count( $value ) ) {
				return true;
			}

			return false;
		}
	}
}

if ( ! function_exists( 'wpenon_show_on_number_higher' ) ) {
	function wpenon_show_on_number_higher( $value, $number_to_compare, $allow_equal = true ) {
		$value             = \WPENON\Util\Parse::float( $value );
		$number_to_compare = \WPENON\Util\Parse::float( $number_to_compare );

		if ( $value > $number_to_compare ) {
			return true;
		} elseif ( $value == $number_to_compare && $allow_equal ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wpenon_show_on_number_lower' ) ) {
	function wpenon_show_on_number_lower( $value, $number_to_compare, $allow_equal = true ) {
		$value             = \WPENON\Util\Parse::float( $value );
		$number_to_compare = \WPENON\Util\Parse::float( $number_to_compare );

		if ( $value < $number_to_compare ) {
			return true;
		} elseif ( $value == $number_to_compare && $allow_equal ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wpenon_show_on_array_whitelist' ) ) {
	function wpenon_show_on_array_whitelist( $value, $whitelist ) {
		$whitelist = \WPENON\Util\Parse::arr( $whitelist );

		if ( in_array( $value, $whitelist ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wpenon_show_on_array_whitelist_2' ) ) {
	function wpenon_show_on_array_whitelist_2( $value1, $whitelist1, $value2, $whitelist2 ) {
		$whitelist1 = \WPENON\Util\Parse::arr( $whitelist1 );
		$whitelist2 = \WPENON\Util\Parse::arr( $whitelist2 );
		if ( in_array( $value1, $whitelist1 ) && in_array( $value2, $whitelist2 ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wpenon_show_on_array_blacklist' ) ) {
	function wpenon_show_on_array_blacklist( $value, $blacklist ) {
		$blacklist = \WPENON\Util\Parse::arr( $blacklist );

		if ( ! in_array( $value, $blacklist ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wpenon_show_on_array_dynamic_whitelist' ) ) {
	function wpenon_show_on_array_dynamic_whitelist( $value, $dependency, $whitelists ) {
		if ( isset( $whitelists[ $dependency ] ) ) {
			$whitelist = \WPENON\Util\Parse::arr( $whitelists[ $dependency ] );

			if ( in_array( $value, $whitelist ) ) {
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'wpenon_show_on_array_dynamic_blacklist' ) ) {
	function wpenon_show_on_array_dynamic_blacklist( $value, $dependency, $blacklists ) {
		if ( isset( $blacklists[ $dependency ] ) ) {
			$blacklist = \WPENON\Util\Parse::arr( $blacklists[ $dependency ] );

			if ( ! in_array( $value, $blacklist ) ) {
				return true;
			}

			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'wpenon_show_on_not_empty' ) ) {
	function wpenon_show_on_not_empty( $value ) {
		if ( empty( $value ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wpenon_get_value_by_field' ) ) {
	function wpenon_get_value_by_field( $value, $parse_type = 'string' ) {
		if ( is_callable( '\WPENON\Util\Parse::' . $parse_type ) ) {
			return call_user_func( '\WPENON\Util\Parse::' . $parse_type, $value );
		}

		return $value;
	}
}

if ( ! function_exists( 'wpenon_show_k_baujahr' ) ) {
	function wpenon_show_k_baujahr( $k_info, $k_leistung = '' ) {
		if ( $k_info === 'vorhanden' && $k_leistung === 'groesser' ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'wpenon_get_value_by_whitelist' ) ) {
	function wpenon_get_value_by_whitelist( $value, $whitelist, $parse_type = 'string' ) {
		$whitelist = \WPENON\Util\Parse::arr( $whitelist );
		if ( isset( $whitelist[ $value ] ) ) {
			if ( is_callable( '\WPENON\Util\Parse::' . $parse_type ) ) {
				return call_user_func( '\WPENON\Util\Parse::' . $parse_type, $whitelist[ $value ] );
			}

			return $whitelist[ $value ];
		}

		return null;
	}
}

if ( ! function_exists( 'wpenon_get_value_by_dynamic_whitelist' ) ) {
	function wpenon_get_value_by_dynamic_whitelist( $value, $dependency, $whitelists, $parse_type = 'string' ) {
		if ( isset( $whitelists[ $dependency ] ) ) {
			$whitelist = \WPENON\Util\Parse::arr( $whitelists[ $dependency ] );

			if ( isset( $whitelist[ $value ] ) ) {
				if ( is_callable( '\WPENON\Util\Parse::' . $parse_type ) ) {
					return call_user_func( '\WPENON\Util\Parse::' . $parse_type, $whitelist[ $value ] );
				}

				return $whitelist[ $value ];
			}
		}

		return null;
	}
}

if ( ! function_exists( 'wpenon_get_value_by_sum' ) ) {
	function wpenon_get_value_by_sum( $sum, $dependency_values = array(), $dependency_statuses = array(), $cancel = false ) {
		$parse_type = 'int';
		if ( is_float( $sum ) ) {
			$parse_type = 'float';
		}
		$zero = call_user_func( '\WPENON\Util\Parse::' . $parse_type, 0.0 );

		foreach ( $dependency_values as $name => $value ) {
			if ( ! isset( $dependency_statuses[ $name ] ) || \WPENON\Util\Parse::boolean( $dependency_statuses[ $name ] ) ) {
				$value = call_user_func( '\WPENON\Util\Parse::' . $parse_type, $value );
				$sum  -= $value;
			} elseif ( $cancel ) {
				break;
			}
		}
		if ( $sum < $zero ) {
			return $zero;
		}

		return $sum;
	}
}

if ( ! function_exists( 'wpenon_get_location_by_plz' ) ) {
	function wpenon_get_location_by_plz( $plz, $field = 'ort' ) {
		$location = wpenon_get_table_results(
			'regionen',
			array(
				'postleitzahl' => array(
					'value'   => $plz,
					'compare' => '=',
				),
			),
			array(),
			true
		);
		if ( $location ) {
			if ( isset( $location->$field ) ) {
				return $location->$field;
			}
		}

		return null;
	}
}
