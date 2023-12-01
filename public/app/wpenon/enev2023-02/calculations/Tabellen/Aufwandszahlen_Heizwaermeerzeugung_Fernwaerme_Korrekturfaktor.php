<?php

namespace Enev\Schema202302\Calculations\Tabellen;

use Heizungsanlage;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnung der Daten zur Mittleren Belastung aus Tablle 78.
 *
 * @package
 */
class Aufwandszahlen_Heizwaermeerzeugung_Fernwaerme_Korrekturfaktor {
	/**
	 * ßhg.
	 *
	 * @var string
	 */
	protected string $ßhg;

	/**
	 * pn.
	 *
	 * @var int
	 */
	protected int $pn;

	/**
	 * Heizung im beheizten Bereich.
	 *
	 * @var bool
	 */
	protected bool $heizung_im_beheizten_bereich;

	/**
	 * Übergabe Auslegungstemperatur.
	 *
	 * @var float
	 */
	protected string $uebergabe_auslegungstemperatur;

	/**
	 * Tabellendaten aus Tabelle 79, 80 und 81.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Konstruktor.
	 *
	 * @return void
	 */
	public function __construct( string $ßhg, string $pn, int $heizung_im_beheizten_bereich, string $uebergabe_auslegungstemperatur ) {
		$this->ßhg                            = $ßhg;
		$this->pn                             = $pn;
		$this->heizung_im_beheizten_bereich   = $heizung_im_beheizten_bereich;
		$this->uebergabe_auslegungstemperatur = $uebergabe_auslegungstemperatur;

		$this->table_data = wpenon_get_table_results( 'fern_und_nahwaerme_hausstationen' );
	}

	public function f_temp(): float {
		return $this->interpolierter_wert();
	}

	protected function interpolierter_wert(): float {
		$spalten_keys   = array();
		$spalten_values = array();

		foreach ( $this->spalten() as $spalte ) {
			$spalten_keys[]   = $spalte;
			$spalten_teile    = explode( '.', $spalte );
			$spalten_name     = 'bhg_' . $spalten_teile[0] . '_' . $spalten_teile[1];
			$zeilen_name      = $this->zeile();
			$spalten_values[] = floatval( $this->table_data[ $zeilen_name ]->$spalten_name );
		}

		return interpolate_value( $this->ßhg, $spalten_keys, $spalten_values );
	}

	protected function spalten(): array {
		if ( $this->ßhg <= 0.1 ) {
			return array( 0.1 );
		} elseif ( $this->ßhg > 0.1 && $this->ßhg <= 0.2 ) {
			return array( 0.1, 0.2 );
		} elseif ( $this->ßhg > 0.2 && $this->ßhg <= 0.3 ) {
			return array( 0.2, 0.3 );
		} elseif ( $this->ßhg > 0.3 && $this->ßhg <= 0.4 ) {
			return array( 0.3, 0.4 );
		} elseif ( $this->ßhg > 0.4 && $this->ßhg <= 0.5 ) {
			return array( 0.4, 0.5 );
		} elseif ( $this->ßhg > 0.5 && $this->ßhg <= 0.6 ) {
			return array( 0.5, 0.6 );
		} elseif ( $this->ßhg > 0.6 && $this->ßhg <= 0.7 ) {
			return array( 0.6, 0.7 );
		} elseif ( $this->ßhg > 0.7 && $this->ßhg <= 0.8 ) {
			return array( 0.7, 0.8 );
		} elseif ( $this->ßhg > 0.8 && $this->ßhg <= 0.9 ) {
			return array( 0.8, 0.9 );
		} elseif ( $this->ßhg > 0.9 && $this->ßhg <= 1.0 ) {
			return array( 0.9, 1.0 );
		} else {
			return array( 1.0 );
		}
	}

	protected function zeile(): string {
		$zeile = '';
		if ( $this->heizung_im_beheizten_bereich ) {
			$zeile .= 'bh_';
		} else {
			$zeile .= 'ubh_';
		}

		$zeile .= str_replace( '/', '_', $this->uebergabe_auslegungstemperatur );

		if ( $this->heizung_im_beheizten_bereich && $this->uebergabe_auslegungstemperatur === '35/28' ) {
			if ( $this->pn <= 30 ) {
				$zeile .= '_bis_30';
			} elseif ( $this->pn > 30 && $this->pn <= 100 ) {
				$zeile .= '_bis_100';
			} else {
				$zeile .= '_ab_100';
			}

			return $zeile;
		}

		if ( ( ! $this->heizung_im_beheizten_bereich && $this->uebergabe_auslegungstemperatur === '70/55' ) ||
			( ! $this->heizung_im_beheizten_bereich && $this->uebergabe_auslegungstemperatur === '90/70' )
		) {
			return $zeile;
		}

        if( $this->pn <= 30 ) {
            $zeile . '_bis_30';
        } else {
            $zeile .= '_ab_30';
        }

        return $zeile;
	}
}
