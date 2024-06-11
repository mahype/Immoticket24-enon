<?php

namespace Enev\Schema202404\Calculations\Bauteile;

use Enev\Schema202404\Calculations\Gebaeude\Grundriss;
use Enev\Schema202404\Calculations\Schnittstellen\Transmissionswaerme;

/**
 * Abstrakte Klasse für ein Dach.
 */
abstract class Dach extends Bauteil implements Transmissionswaerme
{
	/**
	 * Grundriss.
	 *
	 * @var Grundriss
	 */
	protected Grundriss $grundriss;

	/**
	 * Höhe des Dachs.
	 *
	 * @var float
	 */
	protected float $hoehe;

	/**
	 * Volumen des Dachs.
	 *
	 * @var float
	 */
	protected float $volumen;

	/**
	 * Fläche des Dachs.
	 *
	 * @var float
	 */
	protected float $flaeche;

	/**
	 * Höhe des Kniestocks.
	 *
	 * @var float
	 */
	protected float $kniestock_hoehe = 0.0;

	/**
	 * Wandfläche in m².
	 * 
	 * @var float
	 */
	protected float $wand_flaeche = 0.0;

	/**
	 * Dachwandflächen.
	 *
	 * @var array
	 */
	protected array $wand_flaechen = array();

	/**
	 * Dämmung des Bauteils.
	 *
	 * @var float
	 */
	protected float $daemmung;

	/**
	 * Konstruktor.
	 *
	 * @param Grundriss $grundriss
	 * @param string    $name
	 * @param float     $flaeche
	 * @param float     $uwert
	 * @return void
	 */
	public function __construct(Grundriss $grundriss, string $name, float $hoehe, float $kniestock_hoehe, float $uwert, float $daemmung)
	{
		$this->grundriss       = $grundriss;
		$this->name            = $name;
		$this->uwert           = $uwert;
		$this->hoehe           = $hoehe;
		$this->kniestock_hoehe = $kniestock_hoehe;
		$this->daemmung        = $daemmung;

		$this->fx = 1.0;

		$this->berechnen();
	}

	/**
	 * Höhe des Dachs.
	 *
	 * @return float
	 */
	public function hoehe(): float
	{
		return $this->hoehe - $this->kniestock_hoehe;
	}

	/**
	 * Volumen des Dachs.
	 *
	 * @return float
	 */
	public function volumen(): float
	{
		return $this->volumen;
	}

	/**
	 * Fläche des Dachs.
	 *
	 * @return float
	 */
	public function flaeche(): float
	{
		return $this->flaeche;
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
	 * Volumen kniestock.
	 *
	 * @return float
	 */
	public function volumen_kniestock(): float
	{
		return $this->grundriss->flaeche() * $this->kniestock_hoehe;
	}

	/**
	 * Die Wandfläche des Dachs.
	 * 
	 * @return float 
	 */
	public function wand_flaeche(): float
	{
		if ($this->wand_flaeche > 0) {
			return $this->wand_flaeche;
		}

		$wand_flaeche = 0;
		foreach ($this->grundriss->waende() as $wand) {
			$wand_flaeche += $this->wandseite_flaeche($wand) + $this->kniestock_flaeche($wand);
		}

		$this->wand_flaeche = $wand_flaeche;

		return $wand_flaeche;
	}

	/**
	 * Wandfläche kniestock.
	 *
	 * @param string $seite Seite des Kniestocks.
	 *
	 * @return float
	 *
	 * @throws Exception
	 */
	public function kniestock_flaeche(string $seite = null): float
	{
		if ($seite) {
			return $this->grundriss->wand_laenge($seite) * $this->kniestock_hoehe;
		}

		return $this->grundriss->wand_laenge_gesamt() * $this->kniestock_hoehe;
	}

	/**
	 * Wandfläche für eine bestimmte Seite.
	 *
	 * @param string $seite
	 * @return float
	 * @throws Exception
	 */
	public function wandseite_flaeche(string $seite): float
	{
		$wand_flaeche = 0;

		if (array_key_exists($seite, $this->wand_flaechen)) {
			$wand_flaeche += $this->wand_flaechen[$seite];
		}

		return $wand_flaeche;
	}

	/**
	 * Berechnung der Werte.
	 *
	 * @return void
	 */
	abstract protected function berechnen(): void;

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
