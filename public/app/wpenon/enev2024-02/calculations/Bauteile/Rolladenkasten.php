<?php

namespace Enev\Schema202402\Calculations\Bauteile;

use Enev\Schema202402\Calculations\Schnittstellen\Transmissionswaerme;

class Rolladenkasten extends Bauteil implements Transmissionswaerme {
	/**
	 * Himmelsrichtung des Bauteils.
	 *
	 * @var string
	 */
	private string $himmelsrichtung;

	/**
	 * Konstruktor
	 *
	 * @param  float  $flaeche         Fläche
	 *                                 des
	 *                                 Bauteils.
	 * @param  float  $uwert           Uwert des Bauteils.
	 * @param  string $himmelsrichtung Himmelsrichtung des Bauteils.
	 * @param  float  $daemmung        Dämmung des Bauteils.
	 * @param  int    $winkel          Winkel des Bauteils.
	 */
	public function __construct( string $name, float $flaeche, float $uwert, string $himmelsrichtung ) {
		$this->name            = $name;
		$this->flaeche         = $flaeche;
		$this->uwert           = $uwert;
		$this->himmelsrichtung = $himmelsrichtung;

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
}
