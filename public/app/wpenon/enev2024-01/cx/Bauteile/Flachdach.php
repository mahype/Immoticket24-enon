<?php

namespace Enev\Schema202302\Calculations\Bauteile;

use Enev\Schema202302\Calculations\Gebaeude\Grundriss;

require_once __DIR__ . '/Dach.php';

/**
 * Diese Klasse entählt die Funktionen zur Berechnung eines Flachdachs.
 *
 * @package
 */
class Flachdach extends Dach {

	/**
	 * Konstruktor.
	 *
	 * @param Grundriss $grundriss Grundriss des Bauteils.
	 * @param string    $name
	 * @param float     $uwert
	 * @param float     $daemmung
	 * @return void
	 */
	public function __construct( Grundriss $grundriss, string $name, float $uwert, float $daemmung ) {
		$this->name      = $name;
		$this->grundriss = $grundriss;
		$this->uwert     = $uwert;
		$this->daemmung  = $daemmung;

		$this->fx = 1.0;

		$this->berechnen();
	}

	/**
	 * Berechnung des volumens und der Dachfläche-
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function berechnen(): void {
		$this->flaeche = $this->grundriss->flaeche();
		$this->volumen = 0;
		$this->hoehe = 0;
	}
}
