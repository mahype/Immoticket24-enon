<?php

namespace Enev\Schema202302\Calculations\Tabellen;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnung der Daten für die Aufwandszahlen von Umlaufwassererhitzern aus Tablle 82 (Baujahr 1987 bis 1994).
 *
 * @package
 */
class Aufwandszahlen_Umlaufwasserheizer {
	/**
	 * Zielwert für die Spalte.
	 *
	 * @var string
	 */
	protected string $zeile_zielwert;

	/**
	 * Zielwert für die Zeile.
	 *
	 * @var float
	 */
	protected float $spalte_zielwert;

	/**
	 * Tabellendaten aus Tabelle 82.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Konstruktor.
	 *
	 * @param string $pn Pn.
	 * @param float  $ßhg ßhg.
	 *
	 * @return void
	 */
	public function __construct( float $pn, float $ßhg ) {
		$this->zeile_zielwert  = $pn;
		$this->spalte_zielwert = $ßhg;
		$this->table_data      = wpenon_get_table_results( 'aufwandszahlen_umlaufwasserheizer' );
	}

	protected function interpolierter_wert(): float {
		$zeilen_keys   = array();
		$zeilen_values = array();

		foreach ( $this->zeilen() as $zeile ) {
			$spalten_keys   = array();
			$spalten_values = array();

			foreach ( $this->spalten() as $spalte ) {
				$spalten_keys[]   = $spalte;
				$spalten_teile    = explode( '.', $spalte );
				
				if( ! isset( $spalten_teile[1] ) ) {
					$spalten_teile[1] = 0;
				}

				$spalten_name     = 'uwh_' . $spalten_teile[0] . '_' . $spalten_teile[1];
				$spalten_values[] = $this->table_data[ $zeile ]->$spalten_name;
			}

			$zeilen_keys[]   = $zeile;
			$interpolierter_wert = interpolate_value( $this->spalte_zielwert, $spalten_keys, $spalten_values );
			$zeilen_values[] = $interpolierter_wert;
		}

		$interpolierter_wert = interpolate_value( $this->zeile_zielwert, $zeilen_keys, $zeilen_values );
		return $interpolierter_wert;
	}

	public function eg0(): float {
		return $this->interpolierter_wert();
	}

	public function ewg0(): float {
		return $this->interpolierter_wert();
	}

	protected function zeilen(): array {
		if ( $this->zeile_zielwert <= 11 ) {
			return array( 11 );
		} elseif ( $this->zeile_zielwert > 11 && $this->zeile_zielwert <= 18 ) {
			return array( 11, 18 );
		} else {
			return array( 18 );
		}
	}

	protected function spalten(): array {
		if ( $this->spalte_zielwert <= 0.1 ) {
			return array( 0.1 );
		} elseif ( $this->spalte_zielwert > 0.1 && $this->spalte_zielwert <= 0.2 ) {
			return array( 0.1, 0.2 );
		} elseif ( $this->spalte_zielwert > 0.2 && $this->spalte_zielwert <= 0.3 ) {
			return array( 0.2, 0.3 );
		} elseif ( $this->spalte_zielwert > 0.3 && $this->spalte_zielwert <= 0.4 ) {
			return array( 0.3, 0.4 );
		} elseif ( $this->spalte_zielwert > 0.4 && $this->spalte_zielwert <= 0.5 ) {
			return array( 0.4, 0.5 );
		} elseif ( $this->spalte_zielwert > 0.5 && $this->spalte_zielwert <= 0.6 ) {
			return array( 0.5, 0.6 );
		} elseif ( $this->spalte_zielwert > 0.6 && $this->spalte_zielwert <= 0.7 ) {
			return array( 0.6, 0.7 );
		} elseif ( $this->spalte_zielwert > 0.7 && $this->spalte_zielwert <= 0.8 ) {
			return array( 0.7, 0.8 );
		} elseif ( $this->spalte_zielwert > 0.8 && $this->spalte_zielwert <= 0.9 ) {
			return array( 0.8, 0.9 );
		} elseif ( $this->spalte_zielwert > 0.9 && $this->spalte_zielwert <= 1.0 ) {
			return array( 0.9, 1.0 );
		} else {
			return array( 1.0 );
		}
	}
}
