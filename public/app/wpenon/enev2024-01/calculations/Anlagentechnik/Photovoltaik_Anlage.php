<?php

namespace Enev\Schema202401\Calculations\Anlagentechnik;

use Enev\Schema202401\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202401\Calculations\Tabellen\Endenergie_Photovoltaikanlagen;

require_once dirname( __DIR__ ) . '/Tabellen/Endenergie_Photovoltaikanlagen.php';

class Photovoltaik_Anlage {
	/**
	 * Gebaeude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Richtung (Ausrichtung nach Himmelsrichtung).
	 *
	 * @var string
	 */
	protected string $richtung;

	/**
	 * Neigung der Anlage in Grad (0, 30, 45, 60, 90 Grad)
	 *
	 * @var int
	 */
	protected int $neigung;

	/**
	 * Fläche der Anlage in m².
	 *
	 * @var float
	 */
	protected float $flaeche;

	/**
	 * Baujahr.
	 */
	protected int $baujahr;

	/**
	 * Konstruktor.
	 *
	 * @param Gebaeude $gebaeude
	 * @return void
	 */
	public function __construct( Gebaeude $gebaeude, string $richtung, int $neigung, float $flaeche, int $baujahr ) {
		$this->gebaeude = $gebaeude;
		$this->richtung = $richtung;
		$this->neigung  = $neigung;
		$this->baujahr  = $baujahr;
		$this->flaeche  = $flaeche;
	}

	/**
	 * Richtung.
	 *
	 * @return string
	 */
	public function richtung(): string {
		return $this->richtung;
	}

	/**
	 * Neigung.
	 *
	 * @return int
	 */
	public function neigung(): int {
		return $this->neigung;
	}

	/**
	 * Fläche.
	 *
	 * @return float
	 */
	public function flaeche(): float {
		return $this->flaeche;
	}

	/**
	 * Baujahr.
	 *
	 * @return int
	 */
	public function baujahr(): int {
		return $this->baujahr;
	}

	/**
	 * Ertrag der Photovoltaik-Anlage.
	 *
	 * @return float
	 */
	public function Pvans( float $QfStrom ): float {
		// Berechnung des ansetzbaren Strometrages aus der PV-Anlage

		// Damit wir nicht in den Minusbereich kommen und was wir max. ansetzbarer Stromertrag
		// if $Qfstrom < $WfPVHP than
		// $Pvans=$Qfstrom
		// if $Qfstrom>= $WfPVHP than
		// $Pvans = $WfPVHP
		// if Keine PV-Anlage vorhanden than
		// $Pvans=0
		// else???

        if( $QfStrom < $this->WfPVHP() ) {
            return $QfStrom;
        }

        return $this->WfPVHP();
	}

	public function QfprodPV(): float {
		// Bestimmung von $qfprodPV, interpolieren nach Tab. 115 mit folgenden Angaben:
		// Abfrage Kunde: PV-Anlage   Ja/Nein
		// Abfrage Kunde : Ausrichtung der PV-Solaranlage (Nord, Nordost etc.) Dropdown _Menue nach Tab 115 (Tabelle 115 bezieht sich auf PV Anlagen ab 2017 Für PV Anlagen vor 2017 muss der Faktor aus der Tabelle mit 0,135/0,182 multipliziert werden). Siehe DIN 18599 Teil 9 Tabelle B2.
		// Abfrage Kunde: Neigungswinkel (0, 30, 45, 60, 90°)
		// Abfrage Kunde: Fläche der PV-Anlage, $APV

		// Bestimmung Endenergie PV-Anlage im Jahr
		//
		//
		// if PV-Anlage vorhanden than

		// $QfprodPV=$qfprodPV*$APV // kWh/a;

		// else
		// $QfprodPV=0

		$qfprodPV = ( new Endenergie_Photovoltaikanlagen( $this->neigung(), $this->richtung() ) )->qfProdPVi0();

		// Korrektur für Baujahr vor 2017, da die Werte in der Tabelle für Baujahr 2017 gelten (Besprechung mit Jan am 15.12.2023).
		if( $this->baujahr() < 2017 ) {
			$qfprodPV *= 0.135 / 0.182;
		}

		return $qfprodPV * $this->flaeche();
	}

    /**
     * Ausnutzungsgrad der PV-Anlage zur Berechnung nutzbaren Stromertrages.
     * 
     * @return float 
     */
    public function WfPVHP(): float {
        // Bestimmung von $fPVHP (Ausnutzungsgrad der PV-Anlage zur Berechnung nutzbaren Stromertrages); in Abhängikeit der Ausrichtung nach Tab 118, T12 
        // $WfPVHP=$QfprodPV*$fPVHP;

        // Tabelle 118
        $fPVHP = array(
            'n'  => 0.298,
            'no' => 0.302,
            'o'  => 0.343,
            'so' => 0.388,
            's'  => 0.408,
            'sw' => 0.379,
            'w'  => 0.333,
            'nw' => 0.303,       
        );

        return $this->QfprodPV() * $fPVHP[ $this->richtung() ];
    }
}
