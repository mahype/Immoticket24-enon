<?php

namespace Enev\Schema202402\Calculations\Tabellen;

use function Enev\Schema202402\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnung der Daten zur Mittleren Belastung aus Tablle 79, 80 und 81.
 *
 * @package
 */
class Aufwandszahlen_Heizwaermeerzeugung {
	/**
	 * Erzeuger
	 *
	 * @var string
	 */
	protected string $erzeuger;

	/**
	 * Energieträger.
	 *
	 * @var string
	 */
	protected string $energietraeger;

	/**
	 * Übergabe auslegungstemperatur.
	 *
	 * @var float
	 */
	protected string $uebergabe_auslegungstemperatur;

	/**
	 * Spalte Zielwert.
	 *
	 * @var float
	 */
	protected float $spalte_zielwert;

	/**
	 * Heizung im beheizten Bereich.
	 *
	 * @var bool
	 */
	protected bool $heizung_im_beheizten_bereich;

	/**
	 * Tabellendaten aus Tabelle 79, 80 und 81.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Konstruktor.
	 *
	 * @param string $erzeuger Erzeuger (standardkessel, niedertemperaturkessel, brennwertkessel, kleinthermeniedertemperatur, kleinthermebrennwert, pelletfeuerung, gasraumheizer, oelofenverdampfungsverbrenner).
	 * @param string $energietraeger Energieträger (heizoel, erdgas, fluessiggas, biogas, holzpellets, holzhackschnitzel)
	 * @param string $uebergabe_auslegungstemperatur Übergabe Auslegungstemperatur des Übertragunngssystems.
	 * @param float  $ßhg ßhg.
	 * @param bool   $heizung_im_beheizten_bereich Heizung im beheizten Bereich, ja/nein.
	 *
	 * @return void
	 */
	public function __construct( string $erzeuger, string $energietraeger, string $uebergabe_auslegungstemperatur, float $ßhg, bool $heizung_im_beheizten_bereich ) {
		$this->erzeuger                       = $erzeuger;
		$this->energietraeger                 = $energietraeger;
		$this->uebergabe_auslegungstemperatur = $uebergabe_auslegungstemperatur;
		$this->spalte_zielwert                = $ßhg;
		$this->heizung_im_beheizten_bereich   = $heizung_im_beheizten_bereich;

		$this->table_data = wpenon_get_table_results( 'aufwandszahlen_heizwaermeerzeugung' );
	}

	public function fegt(): float {
		if ( $this->erzeuger === 'kleinthermeniedertemperatur' || $this->erzeuger === 'kleinthermebrennwert' ) {
			return 1.0;
		}

		if ( $this->energietraeger === 'holzpellets' || $this->energietraeger === 'holzhackschnitzel' || $this->energietraeger === 'stueckholz' || $this->energietraeger === 'steinkohle' || $this->energietraeger === 'braunkohle' ) {
			return 1.0;
		}

		return $this->interpolierter_wert();
	}

	public function fegtw(): float {
		return $this->fegt();
	}

	protected function interpolierter_wert(): float {
		$spalten_keys   = array();
		$spalten_values = array();

		foreach ( $this->ßhg_werte() as $spalte ) {
			$zeilen_name = $this->zeile();

			$spalten_keys[]   = $spalte;
			$spalten_teile    = explode( '.', $spalte );
			$spalten_name     = $this->heizung_im_beheizten_bereich ? 'bh_' : 'ubh_';

			if( !isset( $spalten_teile[1] ) ) {
				$spalten_teile[1] = 0;
			}

			$spalten_name    .= $spalten_teile[0] . '_' . $spalten_teile[1];
			$spalten_values[] = floatval( $this->table_data[ $zeilen_name ]->$spalten_name );
		}

		return interpolate_value( $this->spalte_zielwert, $spalten_keys, $spalten_values );
	}

	protected function zeile(): string {
		$zeile = '';

		if ( ( $this->erzeuger === 'brennwertkessel' || $this->erzeuger === 'etagenheizung' )  && $this->energietraeger_ist_gas() ) {
			$zeile = 'brennwertkessel_gas';
		}

		if ( ( $this->erzeuger === 'brennwertkessel' || $this->erzeuger === 'etagenheizung' ) && $this->energietraeger_ist_oel() ) {
			$zeile = 'brennwertkessel_oel';
		}

		if ( ( $this->erzeuger === 'standardkessel' || $this->erzeuger === 'niedertemperaturkessel' ) && ( $this->energietraeger_ist_oel() || $this->energietraeger_ist_gas() ) ) {
			$zeile = 'standardkessel_niedertemperaturkessel_gas_oel';
		}

		$zeile .= '_' . str_replace( '/', '_', $this->uebergabe_auslegungstemperatur );

		return $zeile;
	}

	protected function energietraeger_ist_gas(): bool {
		return (
			$this->energietraeger === 'erdgas' ||
			$this->energietraeger === 'biogas' ||
			$this->energietraeger === 'fluessiggas'
		);
	}

	protected function energietraeger_ist_oel(): bool {
		return (
			$this->energietraeger === 'heizoel'
		);
	}

	protected function ßhg_werte(): array {
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
