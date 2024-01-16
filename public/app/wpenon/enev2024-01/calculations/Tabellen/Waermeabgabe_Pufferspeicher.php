<?php

namespace Enev\Schema202401\Calculations\Tabellen;

use function Enev\Schema202401\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Berechnung der Daten zur Mittleren Belastung aus Tablle 9 und 11.
 *
 * @package
 */
class Waermeabgabe_Pufferspeicher {
	/**
	 * Überhabe Vorlauftemperatur.
	 *
	 * @var float
	 */
	protected float $uebergabe_vorlautemperatur;

	/**
	 * Volumen Pufferspeicher.
	 *
	 * @var float
	 */
	protected float $volumen_pufferspeicher;

	/**
	 * Tabellendaten aus Tabelle 50 & 51.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Heizung im beheizten Bereich.
	 *
	 * @var bool
	 */
	protected bool $heizung_im_beheizten_bereich;

	/**
	 * Konstruktor.
	 *
	 * @param float $volumen_pufferspeicher
	 * @param int   $uebergabe_vorlautemperatur
	 * @return void
	 */
	public function __construct( float $volumen_pufferspeicher, int $uebergabe_vorlautemperatur, bool $heizung_im_beheizten_bereich ) {
		$this->uebergabe_vorlautemperatur   = $uebergabe_vorlautemperatur;
		$this->volumen_pufferspeicher       = $volumen_pufferspeicher;
		$this->heizung_im_beheizten_bereich = $heizung_im_beheizten_bereich;

		$this->table_data = wpenon_get_table_results( 'waermeabgabe_pufferspeicher' );
	}

	/**
	 * Q durch interpolation ermitteln.
	 *
	 * @return float
	 */
	public function Q(): float {
		$liter_slugs = $this->liter_slugs();

		if ( count( $liter_slugs ) === 1 ) {
			$reihe  = 'v_' . $liter_slugs[0];
			$spalte = $this->spalte();
			return $this->table_data[ $reihe ]->$spalte;
		}

		foreach ( $liter_slugs as $liter_slug ) {
			$reihe    = 'v_' . $liter_slug;
			$spalte   = $this->spalte();
			$keys[]   = $liter_slug;
			$values[] = $this->table_data[ $reihe ]->$spalte;
		}

		$interpolated_value = interpolate_value( $this->volumen_pufferspeicher, $keys, $values );
		return $interpolated_value;
	}

	protected function spalte(): string {
		return $this->heizung_im_beheizten_bereich ? 'beheizt_' . $this->uebergabe_vorlautemperatur : 'unbeheizt_' . $this->uebergabe_vorlautemperatur;
	}

	protected function liter_slugs(): array {
		if ( $this->volumen_pufferspeicher <= 40 ) {
			return array( 40 );
		} elseif ( $this->volumen_pufferspeicher > 40 && $this->volumen_pufferspeicher <= 75 ) {
			return array( 40, 75 );
		} elseif ( $this->volumen_pufferspeicher > 75 && $this->volumen_pufferspeicher <= 100 ) {
			return array( 75, 100 );
		} elseif ( $this->volumen_pufferspeicher > 100 && $this->volumen_pufferspeicher <= 200 ) {
			return array( 100, 200 );
		} elseif ( $this->volumen_pufferspeicher > 200 && $this->volumen_pufferspeicher <= 300 ) {
			return array( 200, 300 );
		} elseif ( $this->volumen_pufferspeicher > 300 && $this->volumen_pufferspeicher <= 400 ) {
			return array( 300, 400 );
		} elseif ( $this->volumen_pufferspeicher > 400 && $this->volumen_pufferspeicher <= 500 ) {
			return array( 400, 500 );
		} elseif ( $this->volumen_pufferspeicher > 500 && $this->volumen_pufferspeicher <= 600 ) {
			return array( 500, 600 );
		} elseif ( $this->volumen_pufferspeicher > 600 && $this->volumen_pufferspeicher <= 700 ) {
			return array( 600, 700 );
		} elseif ( $this->volumen_pufferspeicher > 700 && $this->volumen_pufferspeicher <= 800 ) {
			return array( 700, 800 );
		} elseif ( $this->volumen_pufferspeicher > 800 && $this->volumen_pufferspeicher <= 900 ) {
			return array( 800, 900 );
		} elseif ( $this->volumen_pufferspeicher > 900 && $this->volumen_pufferspeicher <= 1000 ) {
			return array( 900, 1000 );
		} elseif ( $this->volumen_pufferspeicher > 1000 && $this->volumen_pufferspeicher <= 1100 ) {
			return array( 1000, 1100 );
		} elseif ( $this->volumen_pufferspeicher > 1100 && $this->volumen_pufferspeicher <= 1200 ) {
			return array( 1100, 1200 );
		} elseif ( $this->volumen_pufferspeicher > 1200 && $this->volumen_pufferspeicher <= 1300 ) {
			return array( 1200, 1300 );
		} elseif ( $this->volumen_pufferspeicher > 1300 && $this->volumen_pufferspeicher <= 1400 ) {
			return array( 1300, 1400 );
		} elseif ( $this->volumen_pufferspeicher > 1400 && $this->volumen_pufferspeicher <= 1500 ) {
			return array( 1400, 1500 );
		} else {
			return array( 1500 );
		}
	}
}
