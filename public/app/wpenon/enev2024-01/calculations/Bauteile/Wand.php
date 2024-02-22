<?php
namespace Enev\Schema202401\Calculations\Bauteile;

use Enev\Schema202401\Calculations\Schnittstellen\Transmissionswaerme;

/**
 * Diese Klasse repräsentiert eine Wand.
 *
 * @package Enev\Schema202401\Calculations\Bauteile
 */
class Wand extends Bauteil implements Transmissionswaerme {

	/**
	 * Seite des Bauteils (a, b, c...)
	 *
	 * @var string
	 */
	private string $seite;

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
	 * Gibt an, ob das Bauteil an ein Wohngebäude grenzt.
	 *
	 * @var bool
	 */
	private bool $grenzt_an_wohngebaeude;

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
	public function __construct( string $name, string $seite, float $flaeche, float $uwert, string $himmelsrichtung, float $daemmung, bool $grenzt_an_wohngebaeude = false ) {
		$this->name                   = $name;
		$this->seite                  = $seite;
		$this->flaeche                = $flaeche;
		$this->uwert                  = $uwert;
		$this->himmelsrichtung        = $himmelsrichtung;
		$this->daemmung               = $daemmung;
		$this->grenzt_an_wohngebaeude = $grenzt_an_wohngebaeude;

		$this->fx = 1.0;
	}

	/**
	 * Name der Wand.
	 * 
	 * @return string
	 */
	public function name(): string {
		return $this->name;
	}

	/**
	 * Seite des Bauteils (a, b, c...)
	 *
	 * @return string
	 */
	public function seite(): string {
		return $this->seite;
	}

	/**
	 * Himmelsrichtung des Bauteils.
	 *
	 * @return string
	 */
	public function himmelsrichtung(): string {
		return $this->himmelsrichtung;
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
	 * Fläche reduzuerieren.
	 *
	 * @param  float $reduktion Reduktion der Fläche.
	 *
	 * @return float Neue Fläche.
	 */
	public function flaeche_reduzieren( float $reduktion ): float {
		$this->flaeche -= $reduktion;
		return $this->flaeche;
	}

	/**
	 * Fläche addieren.
	 *
	 * @param  float $addition Addition der Fläche.
	 *
	 * @return float Neue Fläche.
	 */
	public function flaeche_addieren( float $addition ): float {
		$this->flaeche += $addition;
		return $this->flaeche;
	}

	/**
	 * Gibt an, ob das Bauteil an ein Wohngebäude grenzt.
	 *
	 * @return bool
	 */
	public function grenzt_an_wohngebaeude(): bool {
		return $this->grenzt_an_wohngebaeude;
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
