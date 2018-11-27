<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Util;

class Error {
	private $type = 'notice';
	private $origin = '';
	private $message = '';
	private $version = '';

	public function __construct( $type, $origin, $message = '', $version = '' ) {
		if ( empty( $message ) ) {
			$message = __( 'Die Funktion ist veraltet und sollte nicht mehr verwendet werden.', 'wpenon' );
		}
		$this->type    = $type;
		$this->origin  = $origin;
		$this->message = $message;
		$this->version = $version;

		$this->display();
	}

	public function __get( $field ) {
		if ( property_exists( $this, $field ) ) {
			return $this->$field;
		}

		return false;
	}

	public function display() {
		if ( $this->type == 'fatal' ) {
			$title = __( 'Schwerwiegender Fehler in Funktion <strong>%1$s</strong>', 'wpenon' );
		} else {
			$title = __( 'Die Funktion <strong>%1$s</strong> wurde fehlerhaft aufgerufen', 'wpenon' );
		}

		$version = ! empty( $this->version ) ? sprintf( __( 'Diese Fehlermeldung wurde in Version %2$s des Plugins %1$s hinzugefügt.', 'wpenon' ), '&quot;' . WPENON_NAME . '&quot;', $this->version ) : '';
		$output  = sprintf( $title . ': %2$s %3$s', $this->origin, $this->message, $version );

		if ( $this->type == 'fatal' ) {
			wp_die( $output, $title );
		} elseif ( WP_DEBUG && apply_filters( 'doing_it_wrong_trigger_error', true ) ) {
			trigger_error( $output );
		}
	}
}
