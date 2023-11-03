<?php

namespace Enev\Schema202302\Calculations\Bauteile;

use Enev\Schema202302\Calculations\Schnittstellen\Transmissionswaerme;

require_once dirname( __DIR__ ) . '/Schnittstellen/Transmissionwaerme.php';

/**
 * Temporäre Klasse zur Aufnahme der Daten. Später sollen die Bauteile, Transmissions usw. hier berechnet werden.
 */
class Bauteile implements Transmissionswaerme {

	/**
	 * Sammlung aller Bauteile.
	 *
	 * @var array
	 */
	protected array $elemente = array();

	/**
	 * Konstruktor.
	 *
	 * @param array $elemente Bauteile welche hinzugefügt werden sollen.
	 */
	public function __construct( array $elemente = array() ) {
		foreach ( $elemente as $element ) {
			$this->hinzufuegen( $element );
		}
	}

	/**
	 * Anzahl der Bauteile.
	 * 
	 * @return int 
	 */
	public function anzahl(): int {
		return count( $this->elemente );
	}

	/**
	 * Fügt ein Bauteil hinzu.
	 *
	 * @param Bauteil $bauteil
	 */
	public function hinzufuegen( Bauteil $bauteil ) {
		$this->elemente[] = $bauteil;
	}

	/**
	 * Gibt alle Bauteile zurück.
	 *
	 * @return Bauteile[]
	 */
	public function alle(): array {
		return $this->elemente;
	}

	/**
	 * Gibt das erste Bauteil der Sammlung zurück.
	 *
	 * @return Bauteil
	 */
	public function erstes(): Bauteil {
		return $this->elemente[0];
	}

	/**
	 * Filtern der Bauteile.
	 *
	 * @param string $typ Typ des Bauteils. Mögliche Bauteile: Wand, Dach, Boden, Fenster, Rolladenkasten, Heizkoepernische, Anbauwand, Anbaudach.
	 * @param string $himmelsrichtung Himmelsrichtung des Bauteils. Mögliche Himmelsrichtungen: n, no, o, so, s, sw, w, nw.
	 * @param string $seite Seite des Bauteils. Mögliche Seiten: a, b, c, d, e, f, g, h.
	 *
	 * @return Bauteile
	 */
	public function filter( string $typ = null, string $himmelsrichtung = null, string $seite = null ): Bauteile {
		$elemente = array_filter(
			$this->elemente,
			function ( $bauteil ) use ( $typ, $himmelsrichtung, $seite ) {
				$found = true;

				$reflect = new \ReflectionClass( $bauteil );
				$class = $reflect->getShortName();

				if( $typ !== null && ( $class !== ucfirst( $typ ) ) ) {
					$found = false;
				}

				if ( $himmelsrichtung !== null && ( ! method_exists( $bauteil, 'himmelsrichtung' ) || $bauteil->himmelsrichtung() !== $himmelsrichtung ) ) {
					$found = false;
				}

				if ( $seite !== null && ( ! method_exists( $bauteil, 'seite' ) || $bauteil->seite() !== $seite ) ) {
					$found = false;
				}

				return $found;
			}
		);

		return new Bauteile( $elemente );
	}

	/**
	 * Gibt alle Wände des Gebäudes zurück (ohne Anbau).
	 *
	 * @return Wand_Sammlung Sammlung aller Wände.
	 */
	public function waende(): Wand_Sammlung
	{
		$waende = $this->filter( 'Wand' );
		return new Wand_Sammlung( $waende->alle() );
	}



	/**
	 * Gibt alle Wände des Kellers zurück.
	 *
	 * @return Wand_Sammlung Sammlung aller Kellerwände.
	 */
	public function keller_waende(): Wand_Sammlung
	{
		$waende = $this->filter( 'Kellerwand' );
		return new Wand_Sammlung( $waende->alle() );
	}

	/**
	 * Gibt die Flaeche aller Bauteile zurück.
	 *
	 * @return float
	 */
	public function flaeche(): float {
		$flaeche = 0.0;

		foreach ( $this->elemente as $bauteil ) {
			$flaeche += $bauteil->flaeche();
		}

		return $flaeche;
	}

	/**
	 * Transmissionswärmeverlust aller Bauteile.
	 *
	 * @return float
	 */
	public function ht(): float {
		$ht = 0;

		foreach ( $this->elemente as $bauteil ) {
			$ht += $bauteil->ht();
		}

		return $ht;
	}

	/**
	 * Transmissionswärmeverlust aller Fenster.
	 * 
	 * Frage: Ist das so korrekt implementiert?
	 * 
	 * @return float 
	 */
	public function hw(): float {
		$hw = 0;

		$fenster_sammlung = $this->filter( 'Fenster' )->alle();

		foreach ( $fenster_sammlung as $fenster ) {
			$hw += $fenster->ht();
		}

		return $hw;
	}
}
