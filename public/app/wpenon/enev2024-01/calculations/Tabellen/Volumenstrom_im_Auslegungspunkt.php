<?php

namespace Enev\Schema202401\Calculations\Tabellen;

use function Enev\Schema202401\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Volumenstrom im Auslegungspunkt (Tabelle 38)
 *
 * @package
 */
class Volumenstrom_im_Auslegungspunkt {
	/**
	 * Heizlast des Gebäudes.
	 *
	 * @var float
	 */
	protected float $heizlast;

	/**
	 * Typ des Üergabesystems.
	 *
	 * @var string
	 */
	protected string $uebergabesystem_typ;


	/**
	 * Tabellendaten aus Tabelle 39.
	 *
	 * @var array
	 */
	protected array $table_data;


	public function __construct( float $heizlast, string $uebergabesystem_typ ) {
		$this->heizlast   = $heizlast;
      $this->uebergabesystem_typ = $uebergabesystem_typ;
		$this->table_data = $this->table_data = wpenon_get_table_results( 'volumenstrom_im_auslegungspunkt' );
	}

	public function V(): float {
		$keys = $values = array(); // Reset key and value arrays.

		foreach ( $this->heizlast_slugs() as $heizlast_slug ) {
         $spaltenname = $this->uebergabesystem_typ === 'heizkoerper' ? 'hk_10_k' : 'fbh_7_k';
			$keys[]   = floatval( $this->table_data[ $heizlast_slug ]->kw );
			$values[] = (float) $this->table_data[ $heizlast_slug ]->$spaltenname; // in Abhängikeit der Heizlast und Übergabesystems (Heizkörper 10k; sichere Seite), Fußbodenheizung
		}

		$interpolated_value = interpolate_value( $this->heizlast, $keys, $values );

		return $interpolated_value;
	}


	protected function heizlast_slugs(): array {
		if ( $this->heizlast <= 2.5 ) {
			return array( 'va_2_5' );
		} elseif ( $this->heizlast > 2.5 && $this->heizlast <= 5 ) {
			return array( 'va_2_5', 'va_5' );
		} elseif ( $this->heizlast > 5 && $this->heizlast <= 10 ) {
			return array( 'va_5', 'va_10' );
		} elseif ( $this->heizlast > 10 && $this->heizlast <= 20 ) {
			return array( 'va_10', 'va_20' );
		} elseif ( $this->heizlast > 20 && $this->heizlast <= 30 ) {
			return array( 'va_20', 'va_30' );
		} elseif ( $this->heizlast > 30 && $this->heizlast <= 40 ) {
			return array( 'va_30', 'va_40' );
		} elseif ( $this->heizlast > 40 && $this->heizlast <= 50 ) {
			return array( 'va_40', 'va_50' );
		} elseif ( $this->heizlast > 50 && $this->heizlast <= 60 ) {
			return array( 'va_50', 'va_60' );
		} elseif ( $this->heizlast > 60 && $this->heizlast <= 70 ) {
			return array( 'va_60', 'va_70' );
		} elseif ( $this->heizlast > 70 && $this->heizlast <= 80 ) {
			return array( 'va_70', 'va_80' );
		} elseif ( $this->heizlast > 80 && $this->heizlast <= 90 ) {
			return array( 'va_80', 'va_90' );
		} elseif ( $this->heizlast > 90 && $this->heizlast <= 100 ) {
			return array( 'va_90', 'va_100' );
		} elseif ( $this->heizlast > 100 && $this->heizlast <= 110 ) {
			return array( 'va_100', 'va_110' );
		} elseif ( $this->heizlast > 110 && $this->heizlast <= 120 ) {
			return array( 'va_110', 'va_120' );
		} elseif ( $this->heizlast > 120 && $this->heizlast <= 130 ) {
			return array( 'va_120', 'va_130' );
		} elseif ( $this->heizlast > 130 && $this->heizlast <= 140 ) {
			return array( 'va_130', 'va_140' );
		} elseif ( $this->heizlast > 140 && $this->heizlast <= 150 ) {
			return array( 'va_140', 'va_150' );
		} elseif ( $this->heizlast > 150 && $this->heizlast <= 160 ) {
			return array( 'va_150', 'va_160' );
		} elseif ( $this->heizlast > 160 && $this->heizlast <= 170 ) {
			return array( 'va_160', 'va_170' );
		} elseif ( $this->heizlast > 170 && $this->heizlast <= 180 ) {
			return array( 'va_170', 'va_180' );
		} elseif ( $this->heizlast > 180 && $this->heizlast <= 190 ) {
			return array( 'va_180', 'va_190' );
		} elseif ( $this->heizlast > 190 && $this->heizlast <= 200 ) {
			return array( 'va_190', 'va_200' );
		} elseif ( $this->heizlast > 200 && $this->heizlast <= 300 ) {
			return array( 'va_200', 'va_300' );
		} elseif ( $this->heizlast > 300 && $this->heizlast <= 400 ) {
			return array( 'va_300', 'va_400' );
		} elseif ( $this->heizlast > 400 ) {
			return array( 'va_400' );
		}
	}
}
