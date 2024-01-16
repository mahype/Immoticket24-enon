<?php

namespace Enev\Schema202401\Calculations\Anlagentechnik\Heizungsanlagen;

use Enev\Schema202401\Calculations\Anlagentechnik\Heizungsanlage;
use Enev\Schema202401\Calculations\Calculation_Exception;
use Enev\Schema202401\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202401\Calculations\Tabellen\Aufwandszahlen_Heizwaermeerzeugung_Fernwaerme;
use Enev\Schema202401\Calculations\Tabellen\Aufwandszahlen_Heizwaermeerzeugung_Fernwaerme_Korrekturfaktor;

require_once dirname( dirname( __DIR__ ) ) . '/Tabellen/Aufwandszahlen_Heizwaermeerzeugung_Fernwaerme.php';
require_once dirname( dirname( __DIR__ ) ) . '/Tabellen/Aufwandszahlen_Heizwaermeerzeugung_Fernwaerme_Korrekturfaktor.php';

class Fernwaerme extends Heizungsanlage {
	/**
	 * Konstruktor.
	 *
	 * @param Gebaeude $gebaeude Gebäude.
	 * @param string   $erzeuger Erzeuger (fernwaerme).
	 * @param string   $energietraeger $energietraeger Energieträger (fernwaermehzwfossil).
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
		parent::__construct( $gebaeude, $erzeuger, $energietraeger, $baujahr, $gebaeude->heizsystem()->beheizt(), $prozentualer_anteil );
	}

	public function eg0(): float {
		return ( new Aufwandszahlen_Heizwaermeerzeugung_Fernwaerme( $this->gebaeude->heizsystem()->pn() / 1000, $this->ßhg() ) )->eg0();
	}

	public function ßhg(): float {
		// $ßhg = $ßhs * $ehs // mittlere Belastung der *Übergabe Heizung; damit für Alle Erzeuger (einschl Fern/Nahwärme)
		return $this->gebaeude->heizsystem()->ßhs() * $this->gebaeude->heizsystem()->ehs();
	}

	/**
	 * Erlaubte Typen für konventionelle Kessel.
	 *
	 * @return array
	 */
	public static function erlaubte_erzeuger(): array {
		return array(
			'fernwaerme' => array(
				'typ'            => 'fernwaerme',
				'energietraeger' => array(
					'fernwaermekwkfossil' => 'Nahversorger',
					'fernwaermehzwfossil' => 'Fernheizwärme',
				),
			),
		);
	}

    /**
     * fiso
     * 
     * @return float 
     * @throws Calculation_Exception 
     */
	public function fiso(): float {
		// if $Pn < 30 than  // 30kW
		// $fiso = 1,003;
		// if $Pn >= 30 && $Pn <100 than
		// $fiso = 1,001;
		// else
		// $fiso = 1,000;

        $pn = $this->gebaeude->heizsystem()->pn() / 1000;

        if ( $pn < 30 ) {
            return 1.003;
        } elseif ( $pn >= 30 && $pn < 100 ) {
            return 1.001;
        } else {
            return 1.000;
        }
	}

	public function ftemp(): float {
        return ( new Aufwandszahlen_Heizwaermeerzeugung_Fernwaerme_Korrekturfaktor( 
            $this->ßhg(),
            $this->gebaeude->heizsystem()->pn() / 1000,
            $this->gebaeude->heizsystem()->beheizt(),
            $this->gebaeude->heizsystem()->uebergabesysteme()->erstes()->auslegungstemperaturen()
        ) )->f_temp();
	}

	/**
     * Erzeugung Korrekturfaktur für die Heizungsanlage (Ein Ehg Korrigiert wird nicht berechnet laut 18599 Teil 12 Seite 334).
     * 
     * @return float 
     */
    public function ehg(): float {
        return $this->eg0() * $this->fiso() * $this->ftemp();
    }

    public function ewg(): float {
        return 1.0;
    }

	/**
	 * Hilfsenergie für Heizunganlage im Bereich Erzeugung.
	 * 
	 * @return float 
	 */
	public function Whg(): float {
		// $Whg=120 //kWh/a //nach T12, Kap. 6.6.7.2  und  T8, S.97  // Da kine weiteren Infos in DIN setzten wir den höhren Wert für die Übergabestation an. geregelt Station
		return 120;
	}

	/**
	 * Hilfsenergie für Warmwasserbereitung.
	 * 
	 * @return float
	 */
	public function Wwg(): float {
        return 0;
    }
}
