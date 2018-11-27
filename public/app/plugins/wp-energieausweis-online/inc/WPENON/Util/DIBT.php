<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class DIBT {
	public static function assignRegistryID( $energieausweis ) {
		if ( $energieausweis->isFinalized() ) {
			if ( ! $energieausweis->isRegistered() ) {
				$response = self::request( 'Datenregistratur', array(
					'doc' => $energieausweis->getXML( 'datenerfassung', 'S', true ),
				) );

				if ( $response ) {
					update_post_meta( $energieausweis->ID, '_wpenon_register_response', $response );

					$response = self::processResponse( $response, $energieausweis->post_title );
					if ( $response ) {
						if ( is_wp_error( $response ) ) {
							return $response;
						}

						update_post_meta( $energieausweis->ID, 'registriernummer', $response['Registriernummer'] );
						update_post_meta( $energieausweis->ID, '_registered', true );

						return true;
					}
				}

				return false;
			}

			return true;
		}

		return false;
	}

	public static function sendData( $energieausweis ) {
		if ( $energieausweis->isFinalized() && $energieausweis->isRegistered() ) {
			if ( ! $energieausweis->isDataSent() ) {
				$credentials = self::getCredentials();

				$response = self::request( 'ZusatzdatenErfassung', array(
					'doc'           => $energieausweis->getXML( 'zusatzdatenerfassung', 'S', true ),
					'ausweisnummer' => $energieausweis->registriernummer,
					'username'      => $credentials['user'],
					'passwort'      => $credentials['password'],
				) );

				if ( $response ) {
					update_post_meta( $energieausweis->ID, '_wpenon_data_response', $response );

					$response = self::processResponse( $response, $energieausweis->post_title );
					if ( $response ) {
						if ( is_wp_error( $response ) ) {
							return $response;
						}

						update_post_meta( $energieausweis->ID, '_datasent', true );

						return true;
					}
				}

				return false;
			}

			return true;
		}

		return false;
	}

	public static function getRegistryIDsLeft() {
		$rest = get_transient( 'wpenon_restkontingent' );
		if ( false === $rest ) {
			$credentials = self::getCredentials();

			$response = self::request( 'Restkontingent', array(
				'Username' => $credentials['user'],
				'Passwort' => $credentials['password'],
			) );
			if ( $response ) {
				$response = get_object_vars( $response );
				if ( isset( $response['Kontingent'] ) ) {
					$rest = absint( $response['Kontingent'] );
				} else {
					$rest = 10;
				}
			} else {
				$rest = 10;
			}

			set_transient( 'wpenon_restkontingent', $rest, HOUR_IN_SECONDS );
		}

		return absint( $rest );
	}

	public static function request( $action, $args = array() ) {
		$request = new \WPENON\Util\DIBTSoapRequest( $action, $args );
		$request->send();

		$response = $request->getResponse();

		if ( $response !== null ) {
			return $response;
		}

		return false;
	}

	public static function processResponse( $response, $energieausweis_name ) {
		$response = json_decode( json_encode( simplexml_load_string( $response ) ), true );
		if ( is_array( $response ) && isset( $response['Rueckgabewert'] ) && is_array( $response['Rueckgabewert'] ) ) {
			if ( isset( $response['Restkontingent'] ) ) {
				set_transient( 'wpenon_restkontingent', absint( $response['Restkontingent'] ), HOUR_IN_SECONDS );
			}

			if ( intval( $response['Rueckgabewert']['id'] ) == 0 ) {
				return $response;
			} else {
				new \WPENON\Util\Error( 'notice', __METHOD__, sprintf( __( 'DIBT API Fehler bei Ausweis %1$s: %2$s', 'wpenon' ), $energieausweis_name, $response['Rueckgabewert']['value'] ), '1.0.0' );

				return new \WP_Error( sprintf( 'dibt_error_%s', $response['Rueckgabewert']['id'] ), $response['Rueckgabewert']['value'] );
			}
		}

		return false;
	}

	public static function getLoginURL() {
		$slug = 'published';
		if ( WPENON_DEBUG ) {
			$slug = 'sandbox';
		}

		return 'https://energieausweis.dibt.de/' . $slug . '/account/Login.aspx?app=EnergieKlima&ReturnUrl=%2f' . $slug . '%2fenergieausweis';
	}

	public static function getCredentials() {
		$data = array();

		if ( WPENON_DEBUG ) {
			if ( defined( 'WPENON_DIBT_DEBUG_USER' ) ) {
				$data['user'] = WPENON_DIBT_DEBUG_USER;
			} else {
				new \WPENON\Util\Error( 'fatal', __METHOD__, sprintf( __( 'Die geforderte Konstante %s ist nicht deklariert.', 'wpenon' ), '<code>WPENON_DIBT_DEBUG_USER</code>' ), '1.0.0' );
			}
			if ( defined( 'WPENON_DIBT_DEBUG_PASSWORD' ) ) {
				$data['password'] = md5( WPENON_DIBT_DEBUG_PASSWORD );
			} else {
				new \WPENON\Util\Error( 'fatal', __METHOD__, sprintf( __( 'Die geforderte Konstante %s ist nicht deklariert.', 'wpenon' ), '<code>WPENON_DIBT_DEBUG_PASSWORD</code>' ), '1.0.0' );
			}
		} else {
			if ( defined( 'WPENON_DIBT_USER' ) ) {
				$data['user'] = WPENON_DIBT_USER;
			} else {
				new \WPENON\Util\Error( 'fatal', __METHOD__, sprintf( __( 'Die geforderte Konstante %s ist nicht deklariert.', 'wpenon' ), '<code>WPENON_DIBT_USER</code>' ), '1.0.0' );
			}
			if ( defined( 'WPENON_DIBT_PASSWORD' ) ) {
				$data['password'] = md5( WPENON_DIBT_PASSWORD );
			} else {
				new \WPENON\Util\Error( 'fatal', __METHOD__, sprintf( __( 'Die geforderte Konstante %s ist nicht deklariert.', 'wpenon' ), '<code>WPENON_DIBT_PASSWORD</code>' ), '1.0.0' );
			}
		}

		return $data;
	}
}
