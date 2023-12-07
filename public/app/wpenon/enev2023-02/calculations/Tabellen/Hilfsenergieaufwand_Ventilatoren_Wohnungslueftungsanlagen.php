<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

namespace Enev\Schema202302\Calculations\Tabellen;

use function Enev\Schema202302\Calculations\Helfer\interpolate_value;

require_once dirname( __DIR__ ) . '/Helfer/Math.php';

/**
 *  Hilfsenergieaufwand der Ventilatoren in Wohnungslüftungsanlagen - Tabelle 120.
 */
class Hilfsenergieaufwand_Ventilatoren_Wohnungslueftungsanlagen {
	/**
	 * Zielwert für die Spalte.
	 *
	 * @var string
	 */
	protected string $lueftungssystem;

	/**
	 * Zielwert für die Zeile.
	 *
	 * @var float
	 */
	protected string $flaeche;

	/**
	 * Zielwert für die Zeile.
	 *
	 * @var int
	 */
	protected int $baujahr;

	/**
	 * Tabellendaten aus Tabelle 122.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Konstruktor.
	 *
	 * @example $a = new Hilfsenergieaufwand_Ventilatoren_Wohnungslueftungsanlagen( 2500, 1989 );
	 * @param int $flaeche Zielwert für die Spalte.
	 * @param int $baujahr Zielwert für die Zeile.
	 *
	 * @return void
	 */
	public function __construct( int $flaeche, int $baujahr ) {
		$this->flaeche    = $flaeche;
		$this->baujahr    = $baujahr;
		$this->table_data = wpenon_get_table_results( 'hilfsenergieaufwand_der_ventilatoren' );
	}

	protected function interpolierter_wert(): float {
		$keys = $values = array(); // Reset key and value arrays.

		foreach ( $this->flaecheslug() as $flaeche_slug ) {
			$keys[] = floatval( $flaeche_slug );
			if ( $this->baujahrslug() === '2009' ) {
				$values[] = (float) $this->table_data[ $flaeche_slug ]->ac;
			} else {
				$values[] = (float) $this->table_data[ $flaeche_slug ]->dc_ec;
			}
		}

		$interpolated_value = interpolate_value( $this->flaeche, $keys, $values );

		return $interpolated_value;
	}

	/**
	 * Baujahr als Slug für die Spalte.
	 *
	 * @return string
	 */
	private function baujahrslug(): string {
		$baujahrslug = '';
		if ( $this->baujahr < 2010 ) {
			$baujahrslug = '2009';
		} elseif ( $this->baujahr > 2009 ) {
			$baujahrslug = '2010';
		}
		return $baujahrslug;
	}

	/**
	 * Flaeche als Slug für die Spalte.
	 *
	 * @return array
	 */
	private function flaecheslug(): array {
		if ( $this->flaeche <= 100 ) {
			return array( 100 );
		} elseif ( $this->flaeche > 100 && $this->flaeche <= 150 ) {
			return array( 100, 150 );
		} elseif ( $this->flaeche > 150 && $this->flaeche <= 200 ) {
			return array( 150, 200 );
		} elseif ( $this->flaeche > 200 && $this->flaeche <= 300 ) {
			return array( 200, 300 );
		} elseif ( $this->flaeche > 300 && $this->flaeche <= 400 ) {
			return array( 300, 400 );
		} elseif ( $this->flaeche > 400 && $this->flaeche <= 500 ) {
			return array( 400, 500 );
		} elseif ( $this->flaeche > 500 && $this->flaeche <= 1000 ) {
			return array( 500, 1000 );
		} elseif ( $this->flaeche > 1000 && $this->flaeche <= 2000 ) {
			return array( 1000, 2000 );
		} elseif ( $this->flaeche > 2000 && $this->flaeche <= 3000 ) {
			return array( 2000, 3000 );
		} elseif ( $this->flaeche > 3000 && $this->flaeche <= 4000 ) {
			return array( 3000, 4000 );
		} elseif ( $this->flaeche > 4000 && $this->flaeche <= 5000 ) {
			return array( 4000, 5000 );
		} elseif ( $this->flaeche > 5000 && $this->flaeche <= 6000 ) {
			return array( 5000, 6000 );
		} elseif ( $this->flaeche > 6000 && $this->flaeche <= 7000 ) {
			return array( 6000, 7000 );
		} elseif ( $this->flaeche > 7000 && $this->flaeche <= 8000 ) {
			return array( 7000, 8000 );
		} elseif ( $this->flaeche > 8000 && $this->flaeche <= 9000 ) {
			return array( 8000, 9000 );
		} elseif ( $this->flaeche > 9000 && $this->flaeche <= 10000 ) {
			return array( 9000, 10000 );
		}
	}

	/**
	 * Umrechnungsfaktor Wfan0.
	 *
	 * @return float
	 */
	public function Wfan0(): float { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		return $this->interpolierter_wert();
	}
}
