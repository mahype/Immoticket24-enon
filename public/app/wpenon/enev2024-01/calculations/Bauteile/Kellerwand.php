<?php

namespace Enev\Schema202401\Calculations\Bauteile;

use Enev\Schema202401\Calculations\Schnittstellen\Transmissionswaerme;

/**
 * Die Klasse Kellerwand.
 */
class Kellerwand extends Bauteil implements Transmissionswaerme {
	/**
	 * Dicke der daemmung der Kellewand.
	 * 
	 * @var float
	 */
	private float $daemmung;

	/**
	 * Konstruktor
	 *
	 * @param  string $name            Name des Bauteils.
	 * @param  string $seite           Seite des Bauteils (a, b, c...).
	 * @param  float  $flaeche         Fläche des Bauteils.
	 * @param  float  $uwert           Uwert des Bauteils.
	 * @param  string $himmelsrichtung Himmelsrichtung des Bauteils.
	 * @param  float  $daemmung        Dämmung des Bauteils.
	 */
	public function __construct( string $name, float $flaeche, float $uwert, float $daemmung ) {		
		$this->name                   = $name;		
		$this->flaeche                = $flaeche;
		$this->uwert                  = $uwert;		
		$this->daemmung               = $daemmung;

		$this->fx = 0.75; // Schlechtester Wert aus Tab c4 18599/T12
	}

	/**
	 * Dämmung des Bauteils.
	 *
	 * @return float
	 */
	public function daemmung(): float {
		return $this->daemmung;
	}

		/**
	 * U-Wert des Bauteils.
	 *
	 * @return float
	 */
	public function uwert(): float {
		if ( $this->daemmung() === 0 ) {
			return $this->uwert;
		}

		$daemmung = $this->daemmung / 100.0;
		$uwert    = 1.0 / ( 1.0 / $this->uwert + $daemmung / 0.04 );

		return $uwert;
	}
}
