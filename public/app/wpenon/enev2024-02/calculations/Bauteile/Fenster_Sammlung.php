<?php

namespace Enev\Schema202402\Calculations\Bauteile;

use Enev\Schema202402\Calculations\Schnittstellen\Transmissionswaerme;

/**
 * Sammlung aller Fenster.
 *
 * @package
 */
class Fenster_Sammlung implements Transmissionswaerme {
	/**
	 * Sammlung aller Fenster.
	 *
	 * @var Fenster[]
	 */
	private array $elemente = array();

	/**
	 * Konstruktor
	 *
	 * @param Fenster[] $fenster
	 * @return void
	 */
	public function __construct( array $fenster = array() ) {
		foreach ( $fenster as $fenster ) {
			$this->hinzufuegen( $fenster );
		}
	}

	/**
	 * Fügt ein Fenster hinzu.
	 *
	 * @param Fenster $fenster
	 * @return void
	 */
	public function hinzufuegen( Fenster $fenster ) {
		$this->elemente[] = $fenster;
	}

	/**
	 * Gibt alle Fenster zurück.
	 *
	 * @return Fenster[]
	 */
	public function alle(): array {
		return $this->elemente;
	}

	/**
	 * Gibt das erste Bauteil der Sammlung zurück.
	 *
	 * @return Fenster
	 */
	public function erstes(): Fenster {
		return $this->elemente[0];
	}

	/**
	 * Filtert die Fenster.
	 *
	 * @return Fenster_Sammlung
	 */
	public function filter( string $himmelsrichtung ): Fenster_Sammlung {
		$elemente = array_filter(
			$this->elemente,
			function ( Fenster $element ) use ( $himmelsrichtung ) {
				$found = true;

				if ( $himmelsrichtung !== null && $element->himmelsrichtung() !== $himmelsrichtung ) {
					$found = false;
				}

				return $found;
			}
		);

		return new Fenster_Sammlung( $elemente );
	}

	/**
	 * Fläche aller Fenster.
	 *
	 * @return float
	 */
	public function flaeche(): float {
		$flaeche = 0.0;

		foreach ( $this->elemente as $element ) {
			$flaeche += $element->flaeche();
		}

		return $flaeche;
	}

	/**
	 * Transmissionswärme der Fenster.
	 *
	 * @return float
	 */
	public function ht(): float {
		$ht = 0.0;

		foreach ( $this->elemente as $element ) {
			$ht += $element->ht();
		}

		return $ht;
	}

	/**
	 * Solare Wärmegewinne
	 * 
	 * @return float 
	 */
	public function qi_solar(): float {
		$qs = 0.0;

		foreach ( $this->elemente as $element ) {
			$qs += $element->qi_solar();
		}

		return $qs;
	}

	/**
	 * Monatliche solare Wärmegewinne.
	 * 
	 * @return float
	 */
	public function qi_solar_monat( string $monat ): float {
		$qi_solar = 0.0;

		foreach ( $this->elemente as $element ) {
			$qi_solar += $element->qi_solar_monat( $monat );
		}

		return $qi_solar;
	}
}
