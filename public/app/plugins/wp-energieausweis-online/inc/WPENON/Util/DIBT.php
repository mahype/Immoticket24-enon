<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class DIBT {
	public static function assignRegistryID( $energieausweis ) {
		self::log( sprintf( 'Energieausweis #%s: Trying assigning registration.', $energieausweis->id ) );

		if ( $energieausweis->isFinalized() ) {
			self::log( sprintf( 'Energieausweis #%s: Energy certificate is finalized.', $energieausweis->id ) );

			if ( ! $energieausweis->isRegistered() ) {
				self::log( sprintf( 'Energieausweis #%s: Energy certificate is not registered.', $energieausweis->id ) );
				$data = $energieausweis->getXML( 'datenerfassung', 'S', true );

				$response = self::request( 'Datenregistratur', array(
					'doc' => $data,
				) );

				self::log( sprintf( 'Energieausweis #%s: Trying assigning registration with sent data: %s Response: %s', $energieausweis->id, var_export( $data, true ), var_export( $response, true ) ) );

				if ( false !== $response ) {
					update_post_meta( $energieausweis->id, '_wpenon_register_response', $response );
					$response = self::processResponse( $response, $energieausweis->post_title );

					self::log( sprintf( 'Energiesausweis #%s: Processed response: %s', $energieausweis->id, var_export( $response, true ) ) );

					if ( false !== $response && ! is_wp_error( $response ) ) {
						$old_value = trim( get_post_meta( $energieausweis->id, 'registriernummer', true ) );
						if( ! empty( $old_value ) ) {
							self::log( sprintf( 'Energiesausweis #%s: There was an old value: %s',$energieausweis->id,  $old_value ), true );
						}

						self::log( sprintf( 'Energiesausweis #%s: Assigning registration succesful!', $energieausweis->id ), true );

						update_post_meta( $energieausweis->id, 'registriernummer', $response['Registriernummer'] );
						update_post_meta( $energieausweis->id, '_registered', true );

						return true;
					} elseif ( is_wp_error( $response ) ) {
						self::log( sprintf( 'Energiesausweis #%s: Processed response resulted with WP Errror: %s', $energieausweis->id, var_export( $response->get_error_messages(), true ) ), true );
						return $response;
					} else {
						self::log( sprintf( 'Energiesausweis #%s: Processed response resulted with no response.', $energieausweis->id ) );
					}
				} else {
					self::log( sprintf( 'Energiesausweis #%s: Assiging registration resulted with false reponse.', $energieausweis->id ) );
				}

				return false;
			} else {
				self::log( sprintf( 'Energieausweis #%s: Already registered.', $energieausweis->id ) );
			}

			return true;
		} else {
			self::log( sprintf( 'Energieausweis #%s: Already finalized.', $energieausweis->id ) );
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
					update_post_meta( $energieausweis->id, '_wpenon_data_response', $response );

					self::log( sprintf( 'Energieausweis #%s: Sending data with response: %s', $energieausweis->id, var_export( $response, true ) ) );

					$response = self::processResponse( $response, $energieausweis->post_title );

					self::log( sprintf( 'Energiesausweis #%s: Processed response: %s', $energieausweis->id, var_export( $response, true ) ) );

					if ( $response ) {
						if ( is_wp_error( $response ) ) {
							self::log( sprintf( 'Energiesausweis #%s: Sending data resulted with WP Errror: %s', $energieausweis->id, var_export( $response->get_error_messages(), true ) ), true );
							return $response;
						}

						self::log( sprintf( 'Energiesausweis #%s: Sending data successful!', $energieausweis->id ), true );

						update_post_meta( $energieausweis->id, '_datasent', true );

						return true;
					}
				} else {
					self::log( sprintf( 'Energiesausweis #%s: Sending data with no response.', $energieausweis->id ) );
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

		self::log( sprintf('Request action "%s" with Args: %s', $action,  var_export( $args, true ) ), true );

		$response = $request->getResponse();

		if ( $response !== null ) {
			self::log( sprintf("Response:\r\n%s",  var_export( $args, true ) ), true );
			return $response;
		}

		self::log( sprintf("Response: failed\r\n %s",  var_export( $request, true ) ), true );

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

	public static function log( $message, $backtrace = false ) {
		if( $backtrace ) {
			ob_start();
			debug_print_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
			$trace = ob_get_contents();
			ob_end_clean();

			$message.= chr(13 ) . $trace;
		}

		$url = $_SERVER['REQUEST_URI'];
		$time = date('Y-m-d H:i:s' );
		$microtime = microtime();

		$line = $time . ' - ' . $microtime .  ' - ' . $url . chr(13) . $message . chr(13 );

		$file = fopen( dirname( dirname( ABSPATH ) ) . '/dibt.log', 'a' );
		fputs( $file, $line  );
		fclose( $file );
	}
}
