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

	public static function float( $num ) {
		$dot_pos   = strrpos( $num, '.' );
		$comma_pos = strrpos( $num, ',' );

		if ( ( $dot_pos > $comma_pos ) && $dot_pos ) {
			$sep_pos = $dot_pos;
		} elseif ( ( $comma_pos > $dot_pos ) && $comma_pos ) {
			$sep_pos = $comma_pos;
		} else {
			$sep_pos = false;
		}

		if ( ! $sep_pos ) {
			return floatval( preg_replace( "/[^0-9]/", "", $num ) );
		}

		$pre = substr( $num, 0, $sep_pos ) ;
		$after = substr( $num, $sep_pos + 1, strlen( $num ) );

		if( 3 === strlen( $after ) && ! $comma_pos ) {
			$pre.=$after;
			$after = 0;
		}

		return floatval(
			preg_replace( "/[^0-9]/", "", $pre ) . '.' . preg_replace( "/[^0-9]/", "", $after )
		);
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
