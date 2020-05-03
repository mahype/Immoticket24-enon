<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

function wpenon_get_energieausweis( $energieausweis = null ) {
	return \WPENON\Model\EnergieausweisManager::getEnergieausweis( $energieausweis );
}

function wpenon_get_table_results( $slug, $rows = array(), $cols = array(), $single = false, $orderby = null, $order = 'ASC' ) {
	$table = \WPENON\Model\TableManager::instance()->getTable( $slug );
	if ( $table === null ) {
		if ( $single ) {
			return false;
		}

		return array();
	}

	return $table->getResults( $rows, $cols, $single, $orderby, $order );
}

function wpenon_get_option( $option ) {
	$settings = \WPENON\Util\Settings::instance();

	$value = apply_filters( 'wpenon_get_option', $settings->$option, $option );

	return $value;
}

function wpenon_interpolate( $keysize, $references = array() ) {
	usort( $references, 'wpenon_sort_interpolation_references' );

	$lower  = - 1;
	$higher = - 1;

	if ( count( $references ) < 0 ) {
		return false;
	}

	foreach ( $references as $key => $reference ) {
		if ( $keysize < $reference['keysize'] ) {
			$higher = $key;
			break;
		}
		$lower = $key;
	}

	if ( $higher < 0 ) { // high extrapolation
		$lower2 = $lower - 1;
		if ( $lower2 < 0 ) {
			return $references[ $lower ]['value'];
		}

		return $references[ $lower2 ]['value'] - ( $keysize - $references[ $lower2 ]['keysize'] ) * ( $references[ $lower2 ]['value'] - $references[ $lower ]['value'] ) / ( $references[ $lower ]['keysize'] - $references[ $lower2 ]['keysize'] );
	} elseif ( $lower < 0 ) { // low extrapolation
		$higher2 = $higher + 1;
		if ( $higher2 >= count( $references ) ) {
			return $references[ $higher ]['value'];
		}

		return ( ( $references[ $higher ]['value'] - $references[ $higher2 ]['value'] ) * ( $references[ $higher2 ]['keysize'] - $keysize ) ) / ( $references[ $higher2 ]['keysize'] - $references[ $higher ]['keysize'] ) + $references[ $higher2 ]['value'];
	} else { // interpolation
		$range = $references[ $lower ]['value'] - $references[ $higher ]['value'];
		$sub   = $references[ $higher ]['keysize'] - $references[ $lower ]['keysize'];

		return $references[ $higher ]['value'] + ( $sub - ( $keysize - $references[ $lower ]['keysize'] ) ) * ( $range / $sub );
	}
}

function wpenon_sort_interpolation_references( $a, $b ) {
	if ( $a['keysize'] == $b['keysize'] ) {
		return 0;
	}

	return ( $a['keysize'] < $b['keysize'] ) ? - 1 : 1;
}

function wpenon_enqueue_style( $handle, $file, $dependencies = array(), $version = WPENON_VERSION, $suppress_warnings = false ) {
	return \WPENON\Util\Files::enqueueStyle( $handle, $file, $dependencies, $version, $suppress_warnings );
}

function wpenon_enqueue_script( $handle, $file, $dependencies = array(), $version = WPENON_VERSION, $suppress_warnings = false ) {
	return \WPENON\Util\Files::enqueueScript( $handle, $file, $dependencies, $version, $suppress_warnings );
}

function wpenon_maybe_enqueue_style( $handle, $file, $dependencies = array(), $version = WPENON_VERSION ) {
	return \WPENON\Util\Files::enqueueStyle( $handle, $file, $dependencies, $version, true );
}

function wpenon_maybe_enqueue_script( $handle, $file, $dependencies = array(), $version = WPENON_VERSION ) {
	return \WPENON\Util\Files::enqueueScript( $handle, $file, $dependencies, $version, true );
}

function wpenon_get_image_url( $attachment_id, $size = 'thumbnail' ) {
	return \WPENON\Util\ThumbnailHandler::getImageURL( $attachment_id, $size );
}

function wpenon_get_image_path( $attachment_id, $size = 'thumbnail' ) {
	return \WPENON\Util\ThumbnailHandler::getImagePath( $attachment_id, $size );
}

function wpenon_get_formatted_field_value( $field_slug, $energieausweis = null ) {
	if ( ! is_a( $energieausweis, '\WPENON\Model\Energieausweis' ) ) {
		$energieausweis = \WPENON\Model\EnergieausweisManager::getEnergieausweis( $energieausweis );
	}

	if ( is_a( $energieausweis, '\WPENON\Model\Energieausweis' ) ) {
		$v = 'formatted_' . $field_slug;

		return $energieausweis->$v;
	}

	return false;
}

function wpenon_get_reference_date( $format = 'Y-m-d', $energieausweis = null ) {
	$manager = \WPENON\Model\EnergieausweisManager::instance();

	return $manager->getReferenceDate( $format, $energieausweis );
}

function wpenon_get_class( $value, $type ) {
	$classes = \WPENON\Model\EnergieausweisManager::getAvailableClasses( $type );
	$index   = 0;
	foreach ( $classes as $class => $required_value ) {
		if ( $index >= count( $classes ) - 1 || $value <= $required_value ) {
			return $class;
		}
		$index ++;
	}

	return false;
}

function wpenon_get_dibt_credentials() {
	return \WPENON\Util\DIBT::getCredentials();
}

function wpenon_receipt_shortcode( $atts, $content = null ) {
	add_filter( 'edd_user_can_view_receipt', '__return_true' );
	$output = edd_receipt_shortcode( $atts, $content );
	remove_filter( 'edd_user_can_view_receipt', '__return_true' );

	return $output;
}

function wpenon_version_check() {
	global $wp_version;

	$version_errors = array();

	if ( version_compare( phpversion(), WPENON_REQUIRED_PHP ) < 0 ) {
		$version_errors[] = array(
			'name'     => 'PHP',
			'version'  => phpversion(),
			'required' => WPENON_REQUIRED_PHP,
		);
	}
	if ( version_compare( $wp_version, WPENON_REQUIRED_WP ) < 0 ) {
		$version_errors[] = array(
			'name'     => 'WordPress',
			'version'  => $wp_version,
			'required' => WPENON_REQUIRED_WP,
		);
	}
	if ( ! defined( 'EDD_VERSION' ) || version_compare( EDD_VERSION, WPENON_REQUIRED_EDD ) < 0 ) {
		$version_errors[] = array(
			'name'     => 'Easy Digital Downloads',
			'version'  => defined( 'EDD_VERSION' ) ? EDD_VERSION : __( 'nicht installiert', 'wpenon' ),
			'required' => WPENON_REQUIRED_EDD,
		);
	}

	if ( count( $version_errors ) > 0 ) {
		return json_encode( $version_errors );
	}

	return true;
}

function wpenon_memory_limit_check() {
	$limit = intval( str_replace( 'M', '', ini_get( 'memory_limit' ) ) );
	if ( $limit < WPENON_REQUIRED_MEMORY_LIMIT ) {
		ini_set( 'memory_limit', WPENON_REQUIRED_MEMORY_LIMIT . 'M' );
		$limit = intval( str_replace( 'M', '', ini_get( 'memory_limit' ) ) );
		if ( $limit < WPENON_REQUIRED_MEMORY_LIMIT ) {
			return false;
		}
	}

	return true;
}

function wpenon_display_version_error_notice() {
	if ( WPENON_VERSION_CHECK !== true ) {
		echo '<div class="error">';
		echo '<p><strong>' . sprintf( __( 'Schwerwiegender Fehler im Plugin %s:', 'wpenon' ), WPENON_NAME ) . '</strong> ' . __( 'Ein oder mehrere erforderliche Ressourcen weisen nicht die nötige Version auf.', 'wpenon' ) . '</p>';

		try {
			$errors = json_decode( WPENON_VERSION_CHECK, true );
		} catch ( \Exception $e ) {
			$errors = array();
		}

		if ( count( $errors ) > 0 ) {
			echo '<p>' . __( 'Bitte aktualisieren Sie die folgenden Ressourcen:', 'wpenon' ) . '</p>';
			echo '<table style="width:100%;max-width:650px;">';
			echo '<thead>';
			echo '<tr>';
			echo '<th>' . __( 'Name der Ressource', 'wpenon' ) . '</th>';
			echo '<th>' . __( 'Ihre installierte Version', 'wpenon' ) . '</th>';
			echo '<th>' . __( 'Benötigte Version', 'wpenon' ) . '</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';

			foreach ( $errors as $error ) {
				echo '<tr>';
				echo '<td>' . $error['name'] . '</td>';
				echo '<td>' . $error['version'] . '</td>';
				echo '<td>' . $error['required'] . '</td>';
				echo '</tr>';
			}

			echo '</tbody>';
			echo '</table>';
		}

		echo '</div>';
	}
}

function wpenon_display_memory_limit_error_notice() {
	echo '<div class="error">';
	echo '<p><strong>' . sprintf( __( 'Schwerwiegender Fehler im Plugin %s:', 'wpenon' ), WPENON_NAME ) . '</strong> ' . sprintf( __( 'Ihr Server unterstützt die Mindestanforderung von %d MB Memory Limit nicht.', 'wpenon' ), WPENON_REQUIRED_MEMORY_LIMIT ) . '</p>';
	echo '</div>';
}

function wpenon_get_controller() {
	if ( ! is_admin() || defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return \WPENON\Controller\Frontend::instance();
	}
	if ( is_admin() ) {
		return \WPENON\Controller\Admin::instance();
	}

	return \WPENON\Controller\General::instance();
}

function wpenon_get_model() {
	return wpenon_get_controller()->getModel();
}

function wpenon_get_view() {
	return wpenon_get_controller()->getView();
}

function wpenon_array_map_recursive( $array, $function ) {
	foreach ( $array as $key => &$value ) {
		if ( is_array( $value ) ) {
			$value = wpenon_array_map_recursive( $value, $function );
		} else {
			$value = call_user_func( $function, $value );
		}
	}

	return $array;
}

if ( ! function_exists( 'edd_price_callback' ) ) {
	function edd_price_callback( $args ) {
		global $edd_options;

		if ( isset( $edd_options[ $args['id'] ] ) ) {
			$value = $edd_options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = edd_currency_filter( ' <input type="text" class="' . $size . '-text" id="edd_settings[' . $args['id'] . ']" name="edd_settings[' . $args['id'] . ']" value="' . edd_format_amount( $value ) . '"/> ' );
		$html .= '<label for="edd_settings[' . $args['id'] . ']" style="padding-left: 20px;"> ' . $args['desc'] . '</label>';

		echo $html;
	}
}

if ( ! function_exists( 'edd_sanitize_price' ) ) {
	function edd_sanitize_price( $input ) {
		return edd_sanitize_amount( $input );
	}

	add_filter( 'edd_settings_price_sanitize', 'edd_sanitize_price' );
}

/**
 * Paymill bank events js include like iban validation
 */
add_action('edd_after_cc_fields', function(){
	echo '<script type="text/javascript" src="' . plugin_dir_url(__DIR__) . '/assets/paymill-bank-events.js"></script>';
});
