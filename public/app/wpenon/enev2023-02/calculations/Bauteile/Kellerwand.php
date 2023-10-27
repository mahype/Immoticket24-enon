<?php

namespace Enev\Schema202302\Calculations\Bauteile;

use Enev\Schema202302\Calculations\Schnittstellen\Transmissionswaerme;

/**
 * Die Klasse Kellerwand.
 */
class Kellerwand extends Wand implements Transmissionswaerme {
	/**
	 * Konstruktor
	 *
	 * @param  string $seite           Seite des Bauteils (a, b, c...)
	 * @param  float  $flaeche         Fläche des Bauteils.
	 * @param  float  $uwert           Uwert des Bauteils.
	 * @param  int    $baujahr         Baujahr des Bauteils.
	 * @param  string $himmelsrichtung Himmelsrichtung des Bauteils.
	 * @param  float  $daemmung        Dämmung des Bauteils.
	 */
	public function __construct( string $name, string $seite, float $flaeche, float $uwert, string $himmelsrichtung, float $daemmung ) {
		parent::__construct( $name, $seite, $flaeche, $uwert, $himmelsrichtung, $daemmung );
		$this->fx = 0.75; // Schlechtester Wert aus Tab c4 18599/T12
	}
}
