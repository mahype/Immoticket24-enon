<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

namespace Enev\Schema202402\Calculations\Tabellen;

/**
 * Umrechnungsfaktoren_Kollektorflaeche aus Tablle 63, Tabelle 64 und Tabelle 65.
 *
 * @package
 */
class Umrechnungsfaktoren_Kollektorflaeche {
	/**
	 * Zielwert für die Spalte.
	 *
	 * @var string
	 */
	protected string $ausrichtung;

	/**
	 * Zielwert für die Zeile.
	 *
	 * @var float
	 */
	protected float $orientierung;

	/**
	 * Zielwert für die Zeile.
	 *
	 * @var int
	 */
	protected int $baujahr;

	/**
	 * Tabellendaten aus Tabelle 63, 64, 65.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Konstruktor.
	 *
	 * @example $a = new Umrechnungsfaktoren_Kollektorflaeche('NO', 60, 1989 );
	 * @param string $ausrichtung Zielwert für die Spalte.
	 * @param float  $orientierung Zielwert für die Zeile.
	 * @param int    $baujahr Zielwert für die Zeile.
	 *
	 * @return void
	 */
	public function __construct( string $ausrichtung, float $orientierung, int $baujahr ) {
		$this->ausrichtung  = $ausrichtung;
		$this->orientierung = $orientierung;
		$this->baujahr      = $baujahr;
		$this->table_data   = wpenon_get_table_results( 'umrechnungsfaktoren_kollektorflaeche' );
	}

	/**
	 * Orientierung als Slug für die Spalte.
	 *
	 * @return string
	 */
	private function orientierungslug(): string {
		$orientierungslug = '';
		if ( $this->orientierung == 0 ) {
			$orientierungslug = '0_grad';
		} elseif ( $this->orientierung == 30 ) {
			$orientierungslug = '30_grad';
		} elseif ( $this->orientierung == 45 ) {
			$orientierungslug = '45_grad';
		} elseif ( $this->orientierung == 60 ) {
			$orientierungslug = '60_grad';
		} elseif ( $this->orientierung == 90 ) {
			$orientierungslug = '90_grad';
		}
		return $orientierungslug;
	}

	/**
	 * Baujahr als Slug für die Spalte.
	 *
	 * @return string
	 */
	private function baujahrslug(): string {
		$baujahrslug = '';
		if ( $this->baujahr < 1990 ) {
			$baujahrslug = 'vor_1990';
		} elseif ( $this->baujahr >= 1990 && $this->baujahr < 1999 ) {
			$baujahrslug = '1990_1998';
		} elseif ( $this->baujahr > 1998 ) {
			$baujahrslug = 'ab_1999';
		}
		return $baujahrslug;
	}

	/**
	 * Umrechnungsfaktor fAc für die Kollektorfläche.
	 *
	 * @return float
	 */
	public function fAc(): float { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$column_name = $this->orientierungslug() . '_' . $this->baujahrslug();
		$row_name = strtoupper( $this->ausrichtung ) . '_fAc';
		return $this->table_data[ $row_name ]->$column_name;
	}

	/**
	 * Umrechnungsfaktor fQsola für die Kollektorfläche.
	 *
	 * @return float
	 */
	public function fQsola(): float { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		$column_name = $this->orientierungslug() . '_' . $this->baujahrslug();
		$row_name = strtoupper( $this->ausrichtung ) . '_fQsol';
		return $this->table_data[ $row_name ]->$column_name;
	}

}
