<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class Format {
	public static function prefix( $slug ) {
		if ( strpos( $slug, WPENON_PREFIX ) !== 0 ) {
			return WPENON_PREFIX . $slug;
		}

		return $slug;
	}

	public static function unprefix( $slug ) {
		if ( strpos( $slug, WPENON_PREFIX ) === 0 ) {
			return substr( $slug, strlen( WPENON_PREFIX ) );
		}

		return $slug;
	}

	public static function int( $value ) {
		return number_format( floatval( $value ), 0, wpenon_get_option( 'decimal_separator' ), wpenon_get_option( 'thousands_separator' ) );
	}

	public static function float( $value ) {
		return number_format( floatval( $value ), 2, wpenon_get_option( 'decimal_separator' ), wpenon_get_option( 'thousands_separator' ) );
	}

	public static function float_length( $value ) {
		return number_format( floatval( $value ), 2, wpenon_get_option( 'decimal_separator' ), wpenon_get_option( 'thousands_separator' ) );
	}

	public static function boolean( $value ) {
		if ( $value ) {
			return 'true';
		}

		return 'false';
	}

	public static function arr( $value ) {
		return implode( '|', $value );
	}

	public static function date( $value, $date_format = '' ) {
		if ( empty( $value ) ) {
			return '';
		}
		if ( empty( $date_format ) ) {
			$date_format = get_option( 'date_format' );
		}
		$timestamp = strtotime( $value );

		return date_i18n( $date_format, $timestamp );
	}

	public static function datetime( $value, $date_format = '', $time_format = '' ) {
		if ( empty( $date_format ) ) {
			$date_format = get_option( 'date_format' );
		}
		if ( empty( $time_format ) ) {
			$time_format = get_option( 'time_format' );
		}
		$timestamp = strtotime( $value );

		return sprintf( __( '%1$s um %2$s', 'wpenon' ), date_i18n( $date_format, $timestamp ), date_i18n( $time_format, $timestamp ) );
	}

	public static function unit( $unit, $html_entity = true ) {
		$replacements = array(
			'1' => $html_entity ? '&sup1;' : '¹',
			'2' => $html_entity ? '&sup2;' : '²',
			'3' => $html_entity ? '&sup3;' : '³',
		);
		foreach ( $replacements as $character => $replacement ) {
			if ( strpos( $unit, $character ) !== false ) {
				if ( strpos( $unit, $replacement ) === false ) {
					return str_replace( $character, $replacement, $unit );
				}
			}
		}

		return $unit;
	}

	public static function generateTitle( $title, $post_type, $id = null, $after_publish = false ) {
		// only allow alphanumerical signs and the following: -, :, {, }
		$title = preg_replace( '/[^A-Za-z0-9\-:\{\}]/', '', $title );

		$matches = array();
		if ( preg_match_all( '/(\{(.+)\})/U', $title, $matches ) ) {
			$full_matches     = array_unique( $matches[1] );
			$replacer_matches = array_unique( $matches[2] );
			for ( $i = 0; $i < count( $full_matches ); $i ++ ) {
				$replacer = $replacer_matches[ $i ];
				$args     = array();
				if ( strpos( $replacer, ':' ) ) {
					$args     = explode( ':', $replacer );
					$replacer = $args[0];
					array_shift( $args );
				}
				$replacement = '';
				switch ( $replacer ) {
					case 'date':
						$formatstring = 'Y-m-d';
						if ( isset( $args[0] ) ) {
							$formatstring = $args[0];
						}
						$replacement = current_time( $formatstring );
						break;
					case 'count':
						$energieausweise = get_posts( array(
							'post_type'      => $post_type,
							'post_status'    => 'any',
							'posts_per_page' => - 1,
							'fields'         => 'ids',
							'cache_results'  => false,
							'no_found_rows'  => true,
						) );
						$length          = 4;
						if ( isset( $args[0] ) ) {
							$length = absint( $args[0] );
						}
						$count = count( $energieausweise );
						if ( ! $after_publish ) {
							$count += 1;
						}
						$replacement = zeroise( $count, $length );
						break;
					case 'year-count':
						$energieausweise = get_posts( array(
							'post_type'      => $post_type,
							'post_status'    => 'any',
							'posts_per_page' => - 1,
							'fields'         => 'ids',
							'cache_results'  => false,
							'no_found_rows'  => true,
							'year'           => current_time( 'Y' ),
						) );
						$length          = 4;
						if ( isset( $args[0] ) ) {
							$length = absint( $args[0] );
						}
						$count = count( $energieausweise );
						if ( ! $after_publish ) {
							$count += 1;
						}
						$replacement = zeroise( $count, $length );
						break;
					default:
						break;
				}
				$title = str_replace( $full_matches[ $i ], $replacement, $title );
			}
		}

		//TODO: secure title generator to prevent duplicates

		return $title;
	}

	public static function csvEncode( $row = array(), $charset = WPENON_DEFAULT_CHARSET ) {
		foreach ( $row as $key => &$col ) {
			if ( is_numeric( $col ) ) {
				$col = str_replace( '.', ',', $col );
			} else {
				$col = self::utf8Decode( $col, $charset );
			}
		}
		unset( $key );
		unset( $col );

		return $row;
	}

	public static function pdfEncode( $string ) {

		if ( is_array( $string ) ) {
			return array_map( array( '\WPENON\Util\Format', 'pdfEncode' ), $string );
		}

		if ( is_float( $string ) ) {
			return self::float( $string );
		} elseif ( is_int( $string ) ) {
			return self::int( $string );
		}

		$string = str_replace( array( '&euro;', '–' ), array( '_EURO_', '-' ), $string );

		$string = html_entity_decode( $string, ENT_NOQUOTES );

		return str_replace( '_EURO_', chr( 128 ), self::utf8Decode( $string, 'ISO8859-1' ) );
	}

	public static function utf8Encode( $string, $charset = WPENON_DEFAULT_CHARSET ) {
		if ( strtoupper( $charset ) !== 'UTF-8' ) {
			return iconv( $charset, 'UTF-8', $string );
		}

		return $string;
	}

	public static function utf8Decode( $string, $charset = WPENON_DEFAULT_CHARSET ) {
		if ( strtoupper( $charset ) !== 'UTF-8' ) {
			return iconv( 'UTF-8', $charset, $string );
		}

		return $string;
	}

	public static function getFileEncodings() {
		return array(
			'ISO8859-1'      => 'iso-8859-1',
			'ISO8859-2'      => 'iso-8859-2',
			'ISO8859-3'      => 'iso-8859-3',
			'ISO8859-4'      => 'iso-8859-4',
			'ISO8859-5'      => 'iso-8859-5',
			'ISO8859-6'      => 'iso-8859-6',
			'ISO8859-7'      => 'iso-8859-7',
			'ISO8859-8'      => 'iso-8859-8',
			'ISO8859-9'      => 'iso-8859-9',
			'iso-8859-10'    => 'iso-8859-10',
			'iso-8859-11'    => 'iso-8859-11',
			'iso-8859-12'    => 'iso-8859-12',
			'iso-8859-13'    => 'iso-8859-13',
			'iso-8859-14'    => 'iso-8859-14',
			'iso-8859-15'    => 'iso-8859-15',
			'windows-1250'   => 'windows-1250',
			'windows-1251'   => 'windows-1251',
			'windows-1252'   => 'windows-1252',
			'windows-1256'   => 'windows-1256',
			'windows-1257'   => 'windows-1257',
			'macintosh'      => 'Mac Roman',
			'IBM-eucJP'      => 'euc-jp',
			'IBM-eucKR'      => 'koi8-r',
			'IBM-eucTW'      => 'big5',
			'gb2312'         => 'gb2312',
			'UTF-8'          => 'utf-8',
			'utf-7'          => 'utf-7',
			'KSC5601.1987-0' => 'ks_c_5601-1987',
			'TIS-620'        => 'tis-620',
			'SHIFT_JIS'      => 'SHIFT_JIS',
		);
	}
}
