<?php

namespace Enev\Schema202302\Calculations\Bauteile;

class Heizkoerpernische extends Bauteil {
	/**
	 * Himmelsrichtung des Bauteils.
	 *
	 * @var string
	 */
	private string $himmelsrichtung;

	/**
	 * Konstruktor
	 *
	 * @param  string $name            Name des Bauteils.
	 * @param  float  $flaeche         Fläche des Bauteils.
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
