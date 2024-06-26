<?php

namespace Enev\Schema202403\Calculations\Bauteile;

use Enev\Schema202403\Calculations\Gebaeude\Grundriss;
use Enev\Schema202403\Calculations\Schnittstellen\Transmissionswaerme;

require_once __DIR__ . '/Bauteil.php';

/**
 * Die Klasse Boden.
 */
class Boden extends Bauteil implements Transmissionswaerme
{

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
	public function __construct(string $name, float $flaeche, float $uwert, float $daemmung)
	{
		$this->name      = $name;
		$this->flaeche   = $flaeche;
		$this->uwert     = $uwert;
		$this->daemmung  = $daemmung;

		$this->fx = 0.8;
	}

	/**
	 * Dämmung des Bauteils.
	 *
	 * @return float
	 */
	public function daemmung(): float
	{
		return $this->daemmung;
	}

	/**
	 * Fläche des Bauteils.
	 *
	 * @return float
	 */
	public function flaeche(): float
	{
		return $this->flaeche;
	}

	/**
	 * U-Wert des Bauteils.
	 *
	 * @return float
	 */
	public function uwert(): float
	{
		if ($this->daemmung() === 0) {
			return $this->uwert;
		}

		$daemmung = $this->daemmung / 100.0;
		$uwert    = 1.0 / (1.0 / $this->uwert + $daemmung / 0.04);

		return $uwert;
	}
}
