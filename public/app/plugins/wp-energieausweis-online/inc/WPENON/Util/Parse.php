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

	public static function float( $value ) {
		if ( is_float( $value ) ) {
			return $value;
		}

		$decimal_sep   = wpenon_get_option( 'decimal_separator' );
		$thousands_sep = wpenon_get_option( 'thousands_separator' );

		if ( $decimal_sep === ',' ) {
			if ( preg_match( '/\d\.(\d{3})(\.|,|$)/', $value ) ) {
				$value = str_replace( '.', '', $value );
			}
			if ( strpos( $value, $decimal_sep ) !== false ) {
				if ( ( $thousands_sep == '.' || $thousands_sep == ' ' ) && strpos( $value, $thousands_sep ) !== false ) {
					$value = str_replace( $thousands_sep, '', $value );
				} elseif ( empty( $thousands_sep ) && strpos( $value, '.' ) !== false ) {
					$value = str_replace( '.', '', $value );
				}
				$value = str_replace( $decimal_sep, '.', $value );
			}
		} elseif ( $thousands_sep == ',' ) {
			if ( strpos( $value, $thousands_sep ) !== false ) {
				$value = str_replace( $thousands_sep, '', $value );
			}
		}

		$value = preg_replace( '/[^0-9\.]/', '', $value );

		return floatval( $value );
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
