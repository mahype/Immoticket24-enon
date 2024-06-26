<?php

namespace Enev\Schema202404\Calculations\Bauteile;

class Heizkoerpernische extends Bauteil
{
	/**
	 * Himmelsrichtung des Bauteils.
	 *
	 * @var string
	 */
	private string $himmelsrichtung;

	/**
	 * Dämmung des Bauteils.
	 * 
	 * @var float
	 */
	private float $daemmung;

	/**
	 * Konstruktor
	 *
	 * @param  string $name            Name des Bauteils.
	 * @param  float  $flaeche         Fläche des Bauteils.
	 * @param  float  $uwert_wand      Uwert der Wand zur Berechnung des Uwertes der Heizkörpernische je nach Dämmung.
	 * @param  string $himmelsrichtung Himmelsrichtung des Bauteils.
	 * @param  float  $daemmung        Dämmung des Bauteils.
	 * @param  int    $winkel          Winkel des Bauteils.
	 */
	public function __construct(string $name, float $flaeche, float $uwert_wand, string $himmelsrichtung, float $daemmung)
	{
		$this->name            = $name;
		$this->flaeche         = $flaeche;
		$this->himmelsrichtung = $himmelsrichtung;

		if ($daemmung > 0.0) {
			$this->uwert = $uwert_wand;
		} else {
			$this->uwert = $uwert_wand * 2;
		}

		$this->fx = 1.0;
	}

	/**
	 * Himmelsrichtung des Bauteils.
	 *
	 * @return string
	 */
	public function himmelsrichtung(): string
	{
		return $this->himmelsrichtung;
	}
}
