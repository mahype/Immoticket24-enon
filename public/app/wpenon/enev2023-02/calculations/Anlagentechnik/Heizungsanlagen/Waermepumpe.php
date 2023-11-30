<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlagen;

use Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlage;
use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;

class Waermepumpe extends Heizungsanlage {
	/**
	 * Gebaeude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Konstruktor.
	 *
	 * @param Gebaeude $gebaeude Gebäude.
	 * @param string   $erzeuger Erzeuger (waermepumpeluft, waermepumpeerde, waermepumpewasser).
	 * @param int      $baujahr Baujahr der Heizung.
	 * @param int      $prozentualer_anteil Prozentualer Anteil der Heizungsanlage im Heizsystem.
	 *
	 * @return void
	 */
	public function __construct(
		Gebaeude $gebaeude,
		string $erzeuger,
		string $energietraeger,
		int $baujahr,
		int $prozentualer_anteil = 100,
	) {
		parent::__construct( $erzeuger, $energietraeger, $baujahr, $gebaeude->heizsystem()->beheizt(), $prozentualer_anteil );
		$this->gebaeude = $gebaeude;
	}

	/**
	 * Erlaubte Typen für konventionelle Kessel.
	 *
	 * @return array
	 */
	public static function erlaubte_erzeuger(): array {
		return array(
			'waermepumpeluft'   => array(
				'typ'            => 'waermepumpe',
				'energietraeger' => array(
					'strom' => 'Strom',
				),
			),
			'waermepumpewasser' => array(
				'typ'            => 'waermepumpe',
				'energietraeger' => array(
					'strom' => 'Strom',
				),
			),
			'waermepumpeerde'   => array(
				'typ'            => 'waermepumpe',
				'energietraeger' => array(
					'strom' => 'Strom',
				),
			),
		);
	}

	public function θva(): int {
		$auslegungstemperaturen = $this->gebaeude->heizsystem()->uebergabesysteme()->erstes()->auslegungstemperaturen();

		switch ( $auslegungstemperaturen ) {
			case '90/70':
			case '70/55':
			case '55/35':
				return 55;
			case '35/28':
				return 35;
			default:
				throw new Calculation_Exception( 'Die Auslegungstemperatur "' . $auslegungstemperaturen . '" ist nicht erlaubt.' );
		}
	}

	/**
	 * Berechnung der Vorlauftemperatur als Monatsmittel-Wert.
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function θvl(): float {
		// Berechnung von Vorlauftemperatur als Monatsmittel-Wert
		// if "Heizkörper" than
		// $θvl = (($θva-20)*(($calculations['ßhma']/12)^(1/1.3)))+20 ; // 2-Rohrnetz Heizkörper
		// if  "Fußbodenheizung" than
		// $θvl = (($θva-20)*(($calculations['ßhma']/12)^(1/1.1)))+20 ; // 2-Rohrnetz Fußbodenheizung/Wandheizung
		// else???
		//

		//
		// if $θvl < 30 than
		// $θvl = 30;
		// else
		// $θvl = $θvl;

		if ( $this->gebaeude->heizsystem()->uebergabesysteme()->erstes()->typ() == 'heizkoerper' ) {
			$θvl = ( ( $this->θva() - 20 ) * ( ( $this->gebaeude->ßhma() / 12 ) ** ( 1 / 1.3 ) ) ) + 20;
		} else {
			$θvl = ( ( $this->θva() - 20 ) * ( ( $this->gebaeude->ßhma() / 12 ) ** ( 1 / 1.1 ) ) ) + 20;
		}

		if ( $θvl < 30 ) {
			$θvl = 30;
		}

		return $θvl;
	}

	public function ewg(): float {
		return 0;
	}
}
