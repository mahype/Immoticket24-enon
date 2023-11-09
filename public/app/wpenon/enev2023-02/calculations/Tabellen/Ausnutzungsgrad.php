<?php

namespace Enev\Schema202302\Calculations\Tabellen;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnung der Daten zur Mittleren Belastung aus Tablle 9 und 11.
 *
 * @package
 */
class Ausnutzungsgrad {

	protected float $tau;

	protected float $ym;

	/**
	 * Tabellendaten aus Tabelle 9 bei Einfamilienhaus oder Tabelle 11 bei Mehrfamilienhaus.
	 *
	 * @var array
	 */
	protected array $table_data;


	public function __construct( float $tau, float $ym ) {
		$this->tau = $tau;
		$this->ym  = $ym;

		$this->table_data = wpenon_get_table_results( 'ausnutzungsgrad' );
	}

	/**
	 * Tau slugs anhand von Tau ermitteln.
	 *
	 * Dieser wird zur Zusammensetzung der Spaltennamen zur Ermittlung der
	 * Außentemperaturabhängigen Belastung ßem1 benötigt.
	 *
	 * @return array
	 */
	protected function tau_slugs(): array {
		if ( $this->tau <= 30 ) {
			return array( 't30' );
		} elseif ( $this->tau > 30 && $this->tau <= 40 ) {
			return array( 't30', 't40' );
		} elseif ( $this->tau > 40 && $this->tau <= 50 ) {
			return array( 't40', 't50' );
		} elseif ( $this->tau > 50 && $this->tau <= 60 ) {
			return array( 't50', 't60' );
		} elseif ( $this->tau > 60 && $this->tau <= 70 ) {
			return array( 't60', 't70' );
		} elseif ( $this->tau > 70 && $this->tau <= 80 ) {
			return array( 't70', 't80' );
		} elseif ( $this->tau > 80 && $this->tau <= 90 ) {
			return array( 't80', 't90' );
		} elseif ( $this->tau > 90 && $this->tau <= 100 ) {
			return array( 't90', 't100' );
		} elseif ( $this->tau > 100 && $this->tau <= 110 ) {
			return array( 't100', 't110' );
		} elseif ( $this->tau > 110 && $this->tau <= 120 ) {
			return array( 't110', 't120' );
		} elseif ( $this->tau > 120 && $this->tau <= 130 ) {
			return array( 't120', 't130' );
		} elseif ( $this->tau > 130 && $this->tau <= 140 ) {
			return array( 't130', 't140' );
		} elseif ( $this->tau > 140 && $this->tau <= 150 ) {
			return array( 't140', 't150' );
		} else {
			return array( 't150' );
		}
	}

	protected function y_slugs(): array {
		$y_table_values = array();
		foreach ( $this->table_data as $key => $table_row ) {
			$y_table_values[ $key ] = floatval( str_replace( ',', '.', $table_row->y ) );
		}

		asort( $y_table_values );
		$y_values = array();

		foreach ( $y_table_values as $key => $y_table_value ) {
			if ( $this->ym <= $y_table_value ) {
				$current_y = $key;
				break;
			}

			$previous_y = $key;
		}

		if ( isset( $previous_y ) ) {
			$y_values[] = $previous_y;
		}

		if ( isset( $current_y ) ) {
			$y_values[] = $current_y;
		}

		return $y_values;
	}

	public function nm(): float {
		$y_keys = $y_values = array();

		foreach ( $this->y_slugs() as $y_slug ) {
			$tau_keys = $tau_values = array(); // Reset key and value arrays.
			$row      = $this->table_data[ $y_slug ];

			foreach ( $this->tau_slugs() as $tau_slug ) {
				$tau_keys[]   = str_replace( 't', '', $tau_slug );
				$tau_values[] = floatval( str_replace( ',', '.', $row->$tau_slug ) );
			}

			$y_keys[]   = floatval( str_replace( ',', '.', $this->table_data[ $y_slug ]->y ) );
			$y_values[] = interpolate_value( $this->tau, $tau_keys, $tau_values );
		}

		return interpolate_value( $this->ym, $y_keys, $y_values );
	}
}
