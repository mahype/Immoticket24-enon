<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

namespace Enev\Schema202302\Calculations\Tabellen;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 * Betriebsbereitschaftsleistung für Pellet- und Holzhackschnitzelkessel (aktuelle Standardwerte) – Hilfsenergieaufwand - Tabelle 87.
 */
class Betriebsbereitschaftsleistung_Pellet_Holzhackschnitzelkessel {
	/**
	 * Zielwert für die Spalte.
	 *
	 * @var string
	 */
	protected float $Pn; // phpcs:ignore 

	/**
	 * Zielwert für die Zeile.
	 *
	 * @var float
	 */
	protected string $kesselart;

	/**
	 * Tabellendaten aus Tabelle 87.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Konstruktor.
	 *
	 * @example $a = new Betriebsbereitschaftsleistung_Pellet_Holzhackschnitzelkessel( 50, 'pelletkessel' );
	 * @param int    $Pn Zielwert für die Spalte.
	 * @param string $kesselart Art des Kessels. pelletkessel oder holzhackschnitzelkessel.
	 *
	 * @return void
	 */
	public function __construct( float $Pn, string $kesselart ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$this->Pn         = $Pn; // phpcs:ignore 
		$this->kesselart  = $kesselart;
		$this->table_data = wpenon_get_table_results( 'bbleistung_pellet_holzhackschnitzelkessel' );
	}

	/**
	 * Interpolierter Wert.
	 *
	 * @return float
	 */
	protected function interpolierter_wert(): float {
		$keys   = array(); // Reset key and value arrays.
		$values = array(); // Reset key and value arrays.
		foreach ( $this->pnslug() as $pn_slug ) {
			$keys[] = floatval( $pn_slug );
			if ( 'pelletkessel' === $this->kesselart ) {
				$values[] = (float) $this->table_data[ 'bph_' . $pn_slug ]->pk_kw;
			} else {
				$values[] = (float) $this->table_data[ 'bph_' . $pn_slug ]->hk_kw;
			}
		}

		$interpolated_value = interpolate_value( $this->Pn, $keys, $values ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		return $interpolated_value;
	}

	/**
	 * Flaeche als Slug für die Spalte.
	 *
	 * @return array
	 */
	private function pnslug(): array {
		if ( $this->Pn <= 5 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 5 );
		} elseif ( $this->Pn > 5 && $this->Pn <= 10 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 5, 10 );
		} elseif ( $this->Pn > 10 && $this->Pn <= 20 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 10, 20 );
		} elseif ( $this->Pn > 20 && $this->Pn <= 30 ) {    // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 20, 30 );
		} elseif ( $this->Pn > 30 && $this->Pn <= 40 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 30, 40 );
		} elseif ( $this->Pn > 40 && $this->Pn <= 50 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 40, 00 );
		} elseif ( $this->Pn > 50 && $this->Pn <= 60 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 50, 60 );
		} elseif ( $this->Pn > 60 && $this->Pn <= 70 ) {    // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 60, 70 );
		} elseif ( $this->Pn > 70 && $this->Pn <= 80 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 70, 80 );
		} elseif ( $this->Pn > 80 && $this->Pn <= 90 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 80, 90 );
		} elseif ( $this->Pn > 90 && $this->Pn <= 100 ) {   // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 90, 100 );
		} elseif ( $this->Pn > 100 && $this->Pn <= 120 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 100, 120 );
		} elseif ( $this->Pn > 120 && $this->Pn <= 140 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 120, 140 );
		} elseif ( $this->Pn > 140 && $this->Pn <= 160 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 140, 160 );
		} elseif ( $this->Pn > 160 && $this->Pn <= 180 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 160, 180 );
		} elseif ( $this->Pn > 180 && $this->Pn <= 200 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 180, 200 );
		} elseif ( $this->Pn > 200 && $this->Pn <= 250 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 200, 250 );
		} elseif ( $this->Pn > 250 && $this->Pn <= 300 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 250, 300 );
		} elseif ( $this->Pn > 300 && $this->Pn <= 350 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 300, 350 );
		} elseif ( $this->Pn > 350 && $this->Pn <= 400 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return array( 350, 400 );
		}
	}

	/**
	 * Umrechnungsfaktor PhauxP0.
	 *
	 * @return float
	 */
	public function PhauxP0(): float { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return $this->interpolierter_wert();
	}
}
