<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class Validate {
	public static function select( $value, $field ) {
		$error = '';
		if ( ! isset( $field['options'][ $value ] ) && ! empty( $value ) ) {
			$error = __( 'Es wurde ein Wert abgesendet, welcher nicht in der Auswahlliste enthalten ist.', 'wpenon' );
		}

		return self::formatResponse( $value, $field, $error );
	}

	public static function multiselect( $value, $field ) {
		$value = \WPENON\Util\Parse::arr( $value );

		$invalid_values = false;
		$values         = array();
		foreach ( $value as $val ) {
			if ( isset( $field['options'][ $val ] ) ) {
				$values[] = $val;
			} elseif ( ! empty( $val ) ) {
				$invalid_values = true;
			}
		}

		$error = '';
		if ( $invalid_values ) {
			$error = __( 'Es wurden ein oder mehrere Werte abgesendet, welche nicht in der Auswahlliste enthalten sind.', 'wpenon' );
		}

		return self::formatResponse( $values, $field, $error );
	}

	public static function checkbox( $value, $field ) {
		$value = \WPENON\Util\Parse::boolean( $value );

		return self::formatResponse( $value, $field );
	}

	public static function int( $value, $field ) {
		$value = \WPENON\Util\Parse::int( $value );

		$error = '';

		if ( $field['min'] !== false ) {
			if ( $value < absint( $field['min'] ) ) {
				$error = sprintf( __( 'Der eingegebene Wert darf nicht kleiner als %d sein.', 'wpenon' ), \WPENON\Util\Format::int( $field['min'] ) );
			}
		}

		if ( $field['max'] !== false ) {
			if ( $value > absint( $field['max'] ) ) {
				$error = sprintf( __( 'Der eingegebene Wert darf nicht größer als %d sein.', 'wpenon' ), \WPENON\Util\Format::int( $field['max'] ) );
			}
		}

		return self::formatResponse( $value, $field, $error );
	}

	public static function float( $value, $field ) {
		$value = \WPENON\Util\Parse::float( $value );

		$error = '';

		if ( $field['min'] !== false ) {
			if ( $value < floatval( $field['min'] ) ) {
				$error = sprintf( __( 'Der eingegebene Wert darf nicht kleiner als %s sein.', 'wpenon' ), \WPENON\Util\Format::float( floatval( $field['min'] ) ) );
			}
		}
		if ( $field['max'] !== false ) {
			if ( $value > floatval( $field['max'] ) ) {
				$error = sprintf( __( 'Der eingegebene Wert darf nicht größer als %s sein.', 'wpenon' ), \WPENON\Util\Format::float( floatval( $field['max'] ) ) );
			}
		}

		return self::formatResponse( $value, $field, $error );
	}

	public static function zip( $value, $field ) {
		$error = '';

		if ( ! preg_match( '/^[0-9]{5}$/', $value ) || 1001 > (int) $value ) {
			$error = __( 'Der eingegebene Wert ist keine Postleitzahl.', 'wpenon' );
		} else {
			$regions = \WPENON\Model\TableManager::instance()->getTable( 'regionen' );
			if ( ! $regions->getResults( array(
				'postleitzahl' => array(
					'value' => $value,
					'compare' => '>='
				)
			), array(), true ) ) {
				$error = __( 'Die eingegebene Postleitzahl konnte nicht in der Datenbank gefunden werden.', 'wpenon' );
			}
		}

		return self::formatResponse( $value, $field, $error );
	}

	public static function email( $value, $field ) {
		$value = is_email( $value );

		$error = '';
		if ( $value === false ) {
			$error = __( 'Der eingegebene Wert ist keine gültige Email-Adresse.', 'wpenon' );
		}

		return self::formatResponse( $value, $field, $error );
	}

	public static function text( $value, $field ) {
		$value = esc_html( $value );

		$error = '';

		if ( $field['max'] !== false ) {
			if ( strlen( $value ) > absint( $field['max'] ) ) {
				$error = sprintf( __( 'Der eingegebene Text darf nicht länger als %s Zeichen sein.', 'wpenon' ), \WPENON\Util\Format::int( absint( $field['max'] ) ) );
			}
		}

		return self::formatResponse( $value, $field, $error );
	}

	public static function notempty( $value, $field ) {
		if ( is_string( $value ) ) {
			$value = trim( $value );
		}

		$error = __( 'Das Feld darf nicht leer sein.', 'wpenon' );
		$empty = false;
		switch ( $field['type'] ) {
			case 'multiselect':
			case 'multibox':
				$empty = ( count( $value ) < 1 && count( $field['options'] ) > 0 );
				break;
			case 'select':
			case 'radio':
				$empty = ( $value == '' && count( $field['options'] ) > 0 );
				break;
			case 'int':
			case 'float':
				$empty = ( floatval( $value ) <= 0.0 );
				$error = __( 'Der Wert muss größer als 0 sein.', 'wpenon' );
				break;
			default:
				$empty = ( $value == '' );
		}

		if ( ! $empty ) {
			return self::formatResponse( $value, $field );
		}

		return self::formatResponse( $value, $field, $error );
	}

	public static function callback( $value, $field ) {
		if ( is_callable( $field['validate'] ) ) {
			return call_user_func_array( $field['validate'], array( $value, $field ) );
		}

		return self::formatResponse( $value, $field, __( 'Es liegt ein Fehler im System vor. Bitte wenden Sie sich an den Administrator.', 'wpenon' ) );
	}

	public static function formatResponse( $value, $field, $error = '', $warning = '' ) {
		$response = array(
			'value' => $value,
		);

		if ( ! empty( $error ) ) {
			$response['error'] = $error;
		}

		if ( ! empty( $warning ) ) {
			$response['warning'] = $warning;
		}

		return $response;
	}
}
