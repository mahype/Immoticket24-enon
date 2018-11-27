<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class Files {
	public static function enqueueStyle( $handle, $file, $dependencies = array(), $version = WPENON_VERSION, $suppress_warnings = false ) {
		$url = self::getAssetURL( $file, '.css' );
		if ( $url ) {
			wp_enqueue_style( $handle, $url, $dependencies, $version );

			return true;
		} elseif ( ! $suppress_warnings ) {
			new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Der Stylesheet %s konnte nicht gefunden werden.', 'wpenon' ), '<code>' . $handle . '</code>' ), '1.0.0' );
		}

		return false;
	}

	public static function enqueueScript( $handle, $file, $dependencies = array(), $version = WPENON_VERSION, $suppress_warnings = false ) {
		$url = self::getAssetURL( $file, '.js' );
		if ( $url ) {
			wp_enqueue_script( $handle, $url, $dependencies, $version, true );

			return true;
		} elseif ( ! $suppress_warnings ) {
			new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'Das Script %s konnte nicht gefunden werden.', 'wpenon' ), '<code>' . $handle . '</code>' ), '1.0.0' );
		}

		return false;
	}

	public static function getAssetURL( $file, $extension = '' ) {
		if ( strpos( $file, 'http://' ) === 0 || strpos( $file, 'https://' ) === 0 ) {
			if ( strpos( $file, WP_CONTENT_URL ) === 0 ) {
				if ( ! file_exists( str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $file ) ) ) {
					return false;
				}
			}

			return $file;
		} else {
			if ( ! empty( $extension ) && strpos( $file, $extension ) !== strlen( $file ) - strlen( $extension ) ) {
				$file .= $extension;
			}

			if ( strpos( $file, '/' ) !== 0 ) {
				$file = '/' . $file;
			}
			$path = '/assets' . $file;

			$pathinfo = pathinfo( $path );
			$filename = $pathinfo['filename'];
			if ( strpos( $filename, '.min' ) === strlen( $filename ) - 4 ) {
				$filename_min = $filename;
				$filename     = substr( $filename_min, 0, - 4 );
			} else {
				$filename_min = $filename . '.min';
			}

			if ( WPENON_DEBUG ) {
				if ( file_exists( WPENON_PATH . $pathinfo['dirname'] . '/' . $filename . '.' . $pathinfo['extension'] ) ) {
					return WPENON_URL . $pathinfo['dirname'] . '/' . $filename . '.' . $pathinfo['extension'];
				}
				if ( file_exists( WPENON_PATH . $pathinfo['dirname'] . '/' . $filename_min . '.' . $pathinfo['extension'] ) ) {
					return WPENON_URL . $pathinfo['dirname'] . '/' . $filename_min . '.' . $pathinfo['extension'];
				}
			} else {
				if ( file_exists( WPENON_PATH . $pathinfo['dirname'] . '/' . $filename_min . '.' . $pathinfo['extension'] ) ) {
					return WPENON_URL . $pathinfo['dirname'] . '/' . $filename_min . '.' . $pathinfo['extension'];
				}
				if ( file_exists( WPENON_PATH . $pathinfo['dirname'] . '/' . $filename . '.' . $pathinfo['extension'] ) ) {
					return WPENON_URL . $pathinfo['dirname'] . '/' . $filename . '.' . $pathinfo['extension'];
				}
			}
		}

		return false;
	}
}
