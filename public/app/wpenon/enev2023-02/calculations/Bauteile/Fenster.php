<?php

namespace Enev\Schema202302\Calculations\Bauteile;

use Composer\Installers\TYPO3FlowInstaller;
use Enev\Schema202302\Calculations\Schnittstellen\Transmissionswaerme;
use Enev\Schema202302\Calculations\Tabellen\Monatsdaten;

require __DIR__ . '/Bauteil.php';

/**
 * Bauteil Fenster.
 *
 * @package Enev\Schema202302\Calculations\Bauteile
 */
class Fenster extends Bauteil implements Transmissionswaerme {
	/**
	 * Himmelsrichtung des Bauteils.
	 *
	 * @var string
	 */
	private string $himmelsrichtung;

	/**
	 * Winkel des Bauteils.
	 *
	 * @var int
	 */
	private int $winkel;

	/**
	 * G-Wert.
	 *
	 * @var float
	 */
	private float $gwert;

	/**
	 * Konstruktor
	 *
	 * @param  float  $flaeche         Fläche
	 *                                 des
	 *                                 Bauteils.
	 * @param  float  $gwert           Gwert des Bauteils.
	 * @param  float  $uwert           Uwert des Bauteils.
	 * @param  string $himmelsrichtung Himmelsrichtung des Bauteils.
	 * @param  float  $daemmung        Dämmung des Bauteils.
	 * @param  int    $winkel          Winkel des Bauteils.
	 */
	public function __construct( string $name, float $flaeche, float $gwert, float $uwert, string $himmelsrichtung, int $winkel = 90 ) {
		$this->name            = $name;
		$this->flaeche         = $flaeche;
		$this->gwert           = $gwert;
		$this->uwert           = $uwert;
		$this->himmelsrichtung = $himmelsrichtung;
		$this->winkel          = $winkel;

		$this->fx = 1.0;
	}

	/**
	 * Himmelsrichtung des Bauteils.
	 *
	 * @return string
	 */
	public function himmelsrichtung(): string {
		return $this->himmelsrichtung;
	}

	/**
	 * Winkel des Bauteils.
	 *
	 * @return int
	 */
	public function winkel(): int {
		return $this->winkel;
	}

	/**
	 * G-Wert.
	 *
	 * @return float
	 */
	public function gwert(): float {
		return $this->gwert;
	}

	/**
	 * Strahlungsfaktor.
	 *
	 * @param string $monat Monat.
	 *
	 * @return float
	 */
	public function strahlungsfaktor( $monat ) {
		$monatsdaten = new Monatsdaten(); // Todo: Daten global einbinden
		return $monatsdaten->strahlungsfaktor( $monat, $this->winkel, $this->himmelsrichtung );
	}
}
