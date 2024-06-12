<?php

namespace Enev\Schema202404\Calculations\Bauteile;

use Enev\Schema202404\Calculations\Helfer\Jahr;
use Enev\Schema202404\Calculations\Schnittstellen\Transmissionswaerme;
use Enev\Schema202404\Calculations\Tabellen\Monatsdaten;

require_once dirname(__DIR__) . '/Tabellen/Monatsdaten.php';

require_once __DIR__ . '/Bauteil.php';

/**
 * Bauteil Fenster.
 *
 * @package Enev\Schema202404\Calculations\Bauteile
 */
class Fenster extends Bauteil implements Transmissionswaerme
{
	/**
	 * Monatsdaten
	 * 
	 * @var Monatsdaten
	 */
	protected Monatsdaten $monatsdaten;

	/**
	 * Himmelsrichtung des Bauteils.
	 *
	 * @var string
	 */
	private string $himmelsrichtung;

	/**
	 * Winkel des Bauteils.
	 *
	 * @var int
	 */
	private int $winkel;

	/**
	 * G-Wert.
	 *
	 * @var float
	 */
	private float $gwert;

	/**
	 * Konstruktor
	 *
	 * @param  float  $flaeche         Fläche
	 *                                 des
	 *                                 Bauteils.
	 * @param  float  $gwert           Gwert des Bauteils.
	 * @param  float  $uwert           Uwert des Bauteils.
	 * @param  string $himmelsrichtung Himmelsrichtung des Bauteils.
	 * @param  float  $daemmung        Dämmung des Bauteils.
	 * @param  int    $winkel          Winkel des Bauteils.
	 */
	public function __construct(string $name, float $flaeche, float $gwert, float $uwert, string $himmelsrichtung, int $winkel = 90)
	{
		$this->name            = $name;
		$this->flaeche         = $flaeche;
		$this->gwert           = $gwert;
		$this->uwert           = $uwert;
		$this->himmelsrichtung = $himmelsrichtung;
		$this->winkel          = $winkel;

		$this->fx = 1.0;

		$this->monatsdaten = new Monatsdaten();
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

	/**
	 * Winkel des Bauteils.
	 *
	 * @return int
	 */
	public function winkel(): int
	{
		return $this->winkel;
	}

	/**
	 * G-Wert.
	 *
	 * @return float
	 */
	public function gwert(): float
	{
		return $this->gwert;
	}

	/**
	 * Strahlungsfaktor.
	 *
	 * @param string $monat Monat.
	 *
	 * @return float
	 */
	public function strahlungsfaktor($monat)
	{
		return $this->monatsdaten->strahlungsfaktor($monat, $this->winkel, $this->himmelsrichtung);
	}

	/**
	 * Multiplikator für den solaren Gewinn.
	 * 
	 * @param mixed $monat 
	 * @return float 
	 */
	public function solar_gewinn_mpk()
	{
		return 0.9 * 1.0 * 0.7 * $this->gwert();
	}

	/**
	 * Monatlicher solare Gewinn.
	 * 
	 * @param string $monat 
	 * @return float 
	 */
	public function qi_solar_monat(string $monat): float
	{
		return $this->strahlungsfaktor($monat) * $this->solar_gewinn_mpk() * $this->flaeche() * 0.024 * $this->monatsdaten->tage($monat);
	}

	/**
	 * Jährlicher solare Gewinn.
	 * 
	 * @return float 
	 */
	public function qi_solar(): float
	{
		$jahr = new Jahr();
		$summe = 0.0;
		foreach ($jahr->monate() as $monat) {
			$summe += $this->qi_solar_monat($monat->slug());
		}
		return $summe;
	}
}
