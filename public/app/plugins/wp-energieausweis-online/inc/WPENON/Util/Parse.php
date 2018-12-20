<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class Parse {
	public static function int( $value ) {
		return absint( $value );
	}

	private static function to_float( $num ) {
		$dotPos   = strrpos( $num, '.' );
		$commaPos = strrpos( $num, ',' );
		$sep      = ( ( $dotPos > $commaPos ) && $dotPos ) ? $dotPos :
			( ( ( $commaPos > $dotPos ) && $commaPos ) ? $commaPos : false );

		if ( ! $sep ) {
			return floatval( preg_replace( "/[^0-9]/", "", $num ) );
		}

		return floatval(
			preg_replace( "/[^0-9]/", "", substr( $num, 0, $sep ) ) . '.' .
			preg_replace( "/[^0-9]/", "", substr( $num, $sep + 1, strlen( $num ) ) )
		);
	}

	public static function float( $value ) {
		return floatval( number_format( self::to_float( $value ), 2, '.', '' ) );
	}

	public static function boolean( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_numeric( $value ) ) {
			$value = intval( $value );
			if ( $value > 0 ) {
				return true;
			}

			return false;
		}

		$value = strtolower( $value );
		if ( $value === 'true' ) {
			return true;
		}

		return false;
	}

	public static function arr( $value ) {
		if ( is_array( $value ) ) {
			return $value;
		}

		if ( is_object( $value ) ) {
			return get_object_vars( $value );
		}

		if ( strpos( $value, '|' ) !== false ) {
			return explode( '|', $value );
		} elseif ( $value === '' || $value === null ) {
			return array();
		} else {
			return array( $value );
		}
	}
}
