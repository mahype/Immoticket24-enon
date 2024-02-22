<?php

namespace Enev\Schema202402\Calculations\Bauteile;

use Enev\Schema202402\Calculations\Gebaeude\Grundriss;
use Enev\Schema202402\Calculations\Schnittstellen\Transmissionswaerme;

require_once __DIR__ . '/Bauteil.php';

/**
 * Die Klasse Decke.
 */
class Decke extends Bauteil implements Transmissionswaerme {

	/**
	 * Dämmung des Bauteils. 
	 *
	 * @var float
	 */
	protected float $daemmung;

	/**
	 * Grundriss.
	 *
	 * @var Grundriss
	 */
	protected Grundriss $grundriss;

	/**
	 * Konstruktor.
	 *
	 * @param Grundriss $grundriss Fläche des Bauteils.
	 * @param string    $name      Name des Bauteils.
	 * @param float     $uwert     U-Wert des Bauteils.
	 * @param float     $daemmung  Dämmung des Bauteils.
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

		/**
	 * U-Wert des Bauteils.
	 *
	 * @return float
	 */
	public function uwert(): float {
		if ( $this->daemmung() == 0 ) {
			return $this->uwert;
		}

		$daemmung = $this->daemmung / 100.0;
		$uwert    = 1.0 / ( 1.0 / $this->uwert + $daemmung / 0.04 );

		return $uwert;
	}
}
