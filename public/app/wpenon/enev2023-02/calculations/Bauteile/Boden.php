<?php

namespace Enev\Schema202302\Calculations\Bauteile;

use Enev\Schema202302\Calculations\Gebaeude\Grundriss;
use Enev\Schema202302\Calculations\Schnittstellen\Transmissionswaerme;

/**
 * Die Klasse Boden.
 */
class Boden extends Bauteil implements Transmissionswaerme {

	/**
	 * Grundriss.
	 *
	 * @var Grundriss
	 */
	protected Grundriss $grundriss;

	/**
	 * Dämmung des Bauteils.
	 *
	 * @var float
	 */
	protected float $daemmung;

	/**
	 * Konstruktor.
	 *
	 * @param  string $name     Name des Bauteils.
	 * @param  float  $flaeche  Fläche des Bauteils.
	 * @param  float  $uwert    U-Wert des Bauteils.
	 * @param  float  $daemmung Dämmung des Bauteils.
	 */
	public function __construct( Grundriss $grundriss, string $name, float $uwert, float $daemmung ) {
		$this->name      = $name;
		$this->grundriss = $grundriss;
		$this->uwert     = $uwert;
		$this->daemmung  = $daemmung;

		$this->fx = 0.8;
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
	 * Fläche des Bauteils.
	 *
	 * @return float
	 */
	public function flaeche(): float {
		return $this->grundriss->flaeche();
	}
}
