<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik;

use Enev\Schema202302\Calculations\Calculation_Exception;

/**
 * Berechnungen für eine Heizungsanlage.
 */
class Heizungsanlage {
	/**
	 * Typ.
	 * 
	 * @var string
	 */
	protected string $typ;

	/**
	 * Energietraeger.
	 * 
	 * @var string
	 */
	protected string $energietraeger;

	/**
	 * Auslegungstemperaturen.
	 *
	 * @var string
	 */
	protected string $auslegungstemperaturen;

	/**
	 * Welcher Teil der Anlage ist beheizt. Mögliche Werte: 'alles', 'nichts', 'verteilung' oder 'verteilung_erzeuger'.
	 *
	 * @var string
	 */
	protected string $beheizung_anlage;

	/**
	 * Prozentualer Anteil der Heizungsanlage im Heizsystem
	 *
	 * @var int
	 */
	protected int $prozentualer_anteil;

	/**
	 * Konstruktor.
	 *
	 * @param string $typ                    Typ der Heizungsanlage.
	 * @param string $auslegungstemperaturen Auslegungstemperaturen der Heizungsanlage. Mögliche Werte: ' 90/70', '70/55', '55/45' oder '35/28'.
	 * @param string $beheizung_anlage       Welcher Teil der Anlage ist beheizt. Mögliche Werte: 'alles', 'nichts', 'verteilung' oder 'verteilung_erzeuger'.
	 */
	public function __construct( string $typ, string $energietraeger, string $auslegungstemperaturen, string $beheizung_anlage, int $prozentualer_anteil = 100 ) {		
		// Validieren der Auslegungstemperaturen.
		if ( ! $this->validiere_auslegungstemperaturen( $auslegungstemperaturen ) ) {
			throw new Calculation_Exception( 'Auslegungstemperaturen müssen entweder "90/70", "70/55", "55/45" oder "35/28" sein.' );
		}

		// Check der Beheizung der Anlage.
		if ( $beheizung_anlage !== 'alles' && $beheizung_anlage !== 'nichts' && $beheizung_anlage !== 'verteilung' && $beheizung_anlage !== 'verteilung_erzeuger' ) {
			throw new Calculation_Exception( 'Beheizung der Anlage muss entweder "alles", "nichts", "verteilung" oder "verteilung_erzeuger" sein.' );
		}

		$this->typ                    = $typ;
		$this->energietraeger         = $energietraeger;
		$this->auslegungstemperaturen = $auslegungstemperaturen;
		$this->beheizung_anlage       = $beheizung_anlage;
		$this->prozentualer_anteil    = $prozentualer_anteil;
	}

	/**
	 * Typ.
	 * 
	 * @return string
	 */
	public function typ(): string {
		return $this->typ;
	}

	/**
	 * Energietraeger.
	 * 
	 * @return string
	 */
	public function energietraeger(): string {
		return $this->energietraeger;
	}

	/**
	 * Validierung der Auslegungstemperaturen.
	 *
	 * @param string $auslegungstemperaturen
	 *
	 * @return bool
	 */
	protected function validiere_auslegungstemperaturen( string $auslegungstemperaturen ) {
		if ( $auslegungstemperaturen !== '90/70' && $auslegungstemperaturen !== '70/55' && $auslegungstemperaturen !== '55/45' && $auslegungstemperaturen !== '35/28' ) {
			return false;
		}

		return true;
	}

	/**
	 * Auslegungstemperaturen.
	 *
	 * @return string
	 */
	public function auslegungstemperaturen(): string {
		return $this->auslegungstemperaturen;
	}

	/**
	 * Beheizung der Anlage.
	 *
	 * @return string
	 */
	public function beheizung_anlage(): string {
		return $this->beheizung_anlage;
	}

	/**
	 * Prozentualer Anteil.
	 *
	 * @return int
	 */
	public function prozentualer_anteil(): int {
		return $this->prozentualer_anteil;
	}

	/**
	 * Prozentualer Faktor.
	 *
	 * @return float
	 */
	public function prozentualer_faktor(): float {
		return $this->prozentualer_anteil() / 100;
	}

	/**
	 * Nutzbare Wärme.
	 *
	 * @param string $auslegungstemperaturen Auslegungstemperaturen der Heizungsanlage. Mögliche Werte: ' 90/70', '70/55', '55/45' oder '35/28'.
	 *                                       Wenn nicht angegeben, wird der Wert aus dem Konstruktor verwendet. Es können auch andere Auslegungstemperaturen
	 *                                       als die der Heizungsanlage angesetzt werden (beispielsweise die des Übergabesystems).
	 *
	 * @return float Anteils nutzbarer Wärme von Heizungsanlagen (fa-h) aus Tabelle 141 / Teil 12, anteilig für die Heizungsanlage.
	 */
	public function fa_h( $auslegungstemperaturen = null ) {
		// Es können auch andere Auslegungstemperaturen als die der Heizungsanlage angesetzt werden (beispielsweise die des Übergabesystems).
		if ( ! $auslegungstemperaturen ) {
			$auslegungstemperaturen = $this->auslegungstemperaturen;
		}

		if ( ! $this->validiere_auslegungstemperaturen( $auslegungstemperaturen ) ) {
			throw new Calculation_Exception( 'Auslegungstemperaturen müssen entweder "90/70", "70/55", "55/45" oder "35/28" sein.' );
		}

		// Wertzuweisungen je nach Auslegungstemperatur und Beheizung der Anlage.
		switch ( $auslegungstemperaturen ) {
			case '90/70':
				switch ( $this->beheizung_anlage() ) {
					case 'nichts':
						return 0.039;
					case 'verteilung':
						return 0.078;
					case 'alles':
						return 0.123;
					case 'verteilung_erzeuger':
						return 0.118;
				}
				break;
			case '70/55':
				switch ( $this->beheizung_anlage() ) {
					case 'nichts':
						return 0.028;
					case 'verteilung':
						return 0.055;
					case 'alles':
						return 0.099;
					case 'verteilung_erzeuger':
						return 0.095;
				}
				break;
			case '55/45':
				switch ( $this->beheizung_anlage() ) {
					case 'nichts':
						return 0.02;
					case 'verteilung':
						return 0.038;
					case 'alles':
						return 0.082;
					case 'verteilung_erzeuger':
						return 0.077;
				}
				break;
			case '35/28':
				switch ( $this->beheizung_anlage() ) {
					case 'nichts':
						return 0.008;
					case 'verteilung':
						return 0.015;
					case 'alles':
						return 0.057;
					case 'verteilung_erzeuger':
						return 0.053;
				}
		}
	}
}
