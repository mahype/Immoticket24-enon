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
			'waermepumpeluft' => array(
                'energietraeger' => array(
                    'strom' => 'Strom',
                ),
            ),
			'waermepumpewasser' => array(
                'energietraeger' => array(
                    'strom' => 'Strom',
                ),
            ),
			'waermepumpeerde' => array(
                'energietraeger' => array(
                    'strom' => 'Strom',
                ),
            ),
		);
	}

    public function θva(): int {
        $auslegungstemperaturen = $this->gebaeude->heizsystem()->uebergabesysteme()->erstes()->auslegungstemperaturen();

        switch( $auslegungstemperaturen ) {
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
}
