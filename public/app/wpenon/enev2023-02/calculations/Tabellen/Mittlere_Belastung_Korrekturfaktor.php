<?php

namespace Enev\Schema202302\Calculations\Tabellen;

use Enev\Schema202302\Calculations\Calculation_Exception;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

class Mittlere_Belastung_Korrekturfaktor {
	/**
	 * Heizungaanlage beheizt.
	 *
	 * @var bool
	 */
	protected bool $heizungsanlage_beheizt;

	/**
	 * Anzahl der Wohnungen.
	 *
	 * @var int
	 */
	protected int $anzahl_wohnungen;

	/**
	 * Auslegungstemperaturen.
	 *
	 * @var string
	 */
	protected string $auslegungstemperaturen;

	/**
	 * ßhd.
	 *
	 * @var float
	 */
	protected float $ßhd;

	/**
	 * Tabellendaten aus Tabelle 32.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Konstruktor.
	 */
	public function __construct( bool $heizungsanlage_beheizt, int $anzahl_wohnungen, string $auslegunstemperaturen, float $ßhd ) {
		$this->table_data = wpenon_get_table_results( 'mittlere_belastung_korrekturfaktor' );

		$this->heizungsanlage_beheizt = $heizungsanlage_beheizt;
		$this->anzahl_wohnungen       = $anzahl_wohnungen;
		$this->auslegungstemperaturen = $auslegunstemperaturen;
		$this->ßhd                    = $ßhd;
	}

	/**
	 * Mittlere Belastung Korrekturfaktor.
	 *
	 * @return float
	 *
	 * @throws Calculation_Exception
	 */
	public function fßd(): float {
		$slugs  = $this->ßhd_slugs();
		$values = array();

		foreach ( $slugs as $slug => $value ) {
			$column_name = $this->beheizung_slug() . '_' . $this->auslegungstemperaturen_slug() . '_' . $slug;
			$keys[]      = $value;
			$values[]    = floatval( $this->table_data[ $this->rohrnetz_typ() ]->$column_name );
		}

		if ( count( $values ) === 1 ) {
			$fßd = $values[0];
		} else {
			$fßd = interpolate_value( $this->ßhd, $keys, $values );
		}

		return $fßd;
	}

	/**
	 * Beheizung slug.
	 *
	 * @return string
	 */
	protected function beheizung_slug(): string {
		if ( $this->heizungsanlage_beheizt ) {
			return 'beheizt';
		}

		return 'unbeheizt';
	}

	/**
	 * Typ des Rohrnetzes.
	 *
	 * Derzeit nur Etagenringtyp und Steigstrangtyp.
	 *
	 * @return string
	 */
	protected function rohrnetz_typ(): string {
		if ( $this->anzahl_wohnungen === 1 ) {
			return 'etagenringtyp';
		}

		return 'steigstrangtyp';
	}

	/**
	 * Ausleguns-Temperatur Slug.
	 *
	 * @return string
	 *
	 * @throws Calculation_Exception
	 */
	protected function auslegungstemperaturen_slug(): string {
		switch ( $this->auslegungstemperaturen ) {
			case '90/70':
				return '9070';
			case '70/55':
				return '7055';
			case '55/45':
				return '5545';
			case '35/28':
				return '3528';
			default:
				throw new Calculation_Exception( 'Ungültige Auslegungstemperatur.' );
		}
	}

	/**
	 * ßhd Slugs.
	 *
	 * @var string
	 */
	protected function ßhd_slugs(): array {
		$slugs = array();

		if ( $this->ßhd <= 0.1 ) {
			$slugs['01'] = 0.1;
		}

		if ( $this->ßhd > 0.1 && $this->ßhd < 0.3 ) {
			$slugs['02'] = 0.2;
			$slugs['03'] = 0.3;
		}

		if ( $this->ßhd >= 0.3 && $this->ßhd < 0.5 ) {
			$slugs['03'] = 0.3;
			$slugs['05'] = 0.5;
		}

		if ( $this->ßhd >= 0.5 && $this->ßhd < 1.0 ) {
			$slugs['05'] = 0.5;
			$slugs['10'] = 1.0;
		}

		if ( $this->ßhd >= 1.0 ) {
			$slugs['10'] = 1.0;
		}

		return $slugs;
	}
}
