<?php

namespace Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlagen;

use Enev\Schema202302\Calculations\Anlagentechnik\Heizungsanlage;
use Enev\Schema202302\Calculations\Calculation_Exception;
use Enev\Schema202302\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202302\Calculations\Tabellen\COP;
use Enev\Schema202302\Calculations\Helfer\Jahr;

require_once dirname( dirname( __DIR__ ) ) . '/Tabellen/COP.php';
require_once dirname( dirname( __DIR__ ) ) . '/Helfer/Jahr.php';

class Waermepumpe extends Heizungsanlage {
	/**
	 * Jahr.
	 * 
	 * @var Jahr
	 */
	protected Jahr $jahr;

	/**
	 * Gebaeude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * COP Tabelle.
	 * 
	 * @var COP
	 */
	protected COP $cop;

	/**
	 * Findet eine EVU Abschalung statt?
	 * 
	 * @var bool
	 */
	protected bool $evu_abschaltung;

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
		bool $evu_abschaltung = false,
	) {
		parent::__construct( $erzeuger, $energietraeger, $baujahr, $gebaeude->heizsystem()->beheizt(), $prozentualer_anteil );
		$this->gebaeude = $gebaeude;
		$this->evu_abschaltung = $evu_abschaltung;
		$this->jahr = new Jahr();
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
			case '55/45':
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

	/**
	 * COP Tabelle.
	 * 
	 * @return COP 
	 * @throws Calculation_Exception 
	 */
	protected function COP(): COP {
		if ( ! isset( $this->cop ) ) {
			$this->cop = new COP( $this->θvl() );
		}

		return $this->cop;
	}

	/**
	 * COP bei -7°.
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function COPtk_7(): float {
		return $this->COP()->COPtk_7();
	}

	/**
	 * COP bei 2°.
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function COPtk2(): float {
		return $this->COP()->COPtk2();
	}

	/**
	 * COP bei 7°.
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function COPtk7(): float {
		return $this->COP()->COPtk7();
	}

	/**
	 * W-7.
	 * 
	 * @param string $monat 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function W_7_monat( string $monat ): float {
		return $this->COP()->COP_7_Endenergie_Monat( $monat ) * $this->gebaeude->ßoutgmth( $monat );
	}

	/**
	 * W_7 Jahressumme.
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function W_7(): float {	
		$w_7 = 0;
		foreach ( $this->jahr->monate() as $monat ) {
			$w_7 += $this->W_7_monat( $monat->slug() );
		}

		// Nach Absprache mit Jan, wird der Wert von W-7 überschrieben.
		//     if "ja" than  
		//	      $calculations['W-7']=0.016+0,05;
		//     else
		//        $calculations['W-7']=0.016;

		if ( $this->evu_abschaltung ) {
			$w_7 = 0.016 + 0.05;
		} else {
			$w_7 = 0.016;
		}

		return $w_7;
	}	

	/**
	 * W2.
	 * 
	 * @param string $monat 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function W2_monat( string $monat ): float {
		return $this->COP()->COP2_Endenergie_Monat( $monat ) * $this->gebaeude->ßoutgmth( $monat );
	}

	/**
	 * W2 Jahressumme.
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function W2(): float {
		$w2 = 0;
		foreach ( $this->jahr->monate() as $monat ) {
			$w2 += $this->W2_monat( $monat->slug() );
		}

		return $w2;
	}

	/**
	 * W7.
	 * 
	 * @param string $monat 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function W7_monat( string $monat ): float {
		return $this->COP()->COP7_Endenergie_Monat( $monat ) * $this->gebaeude->ßoutgmth( $monat );
	}

	/**
	 * W7 Jahressumme.
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function W7(): float {
		$w7 = 0;
		foreach ( $this->jahr->monate() as $monat ) {
			$w7 += $this->W7_monat( $monat->slug() );
		}

		return $w7;
	}

	public function Qhfwpw_Multiplikator(): float {
		// $calculations['qh']*$UebergabeAufwandszahl['ehce']*$ehdkorr*$ehs;

		$heizsystem = $this->gebaeude->heizsystem();
		$uebergabe = $heizsystem->uebergabesysteme()->erstes();

		$qh = $this->gebaeude->qh();
		$ehce = $uebergabe->ehce();
		$ehd_korrektur = $heizsystem->ehd_korrektur();
		$ehs = $heizsystem->ehs();

		return $qh * $ehce * $ehd_korrektur * $ehs;
	}
	
	/**
	 * Qhfwp.
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function Qhfwp(): float {
		return $this->Qhfwpw_7() + $this->Qhfwpw2() + $this->Qhfwpw7();
	}

	/**
	 * Qhfwpw-7.
	 * 
	 * @return float
	 */
	public function Qhfwpw_7(): float {
		// $calculations['qh']*$UebergabeAufwandszahl['ehce']*$ehdkorr*$ehs*$calculations['W-7'];
		return $this->Qhfwpw_Multiplikator() * $this->W_7();
	}

	/**
	 * Qhfwpw2.
	 * 
	 * @return float
	 */
	public function Qhfwpw2(): float {
		// $calculations['qh']*$UebergabeAufwandszahl['ehce']*$ehdkorr*$ehs*$calculations['W2'];
		return $this->Qhfwpw_Multiplikator() * $this->W2();
	}

	/**
	 * Qhfwpw7.
	 * 
	 * @return float
	 */
	public function Qhfwpw7(): float {
		// $calculations['qh']*$UebergabeAufwandszahl['ehce']*$ehdkorr*$ehs*$calculations['W7'];
		return $this->Qhfwpw_Multiplikator() * $this->W7();
	}

	/**
	 * ewg.
	 * 
	 * @return float 
	 * @throws Calculation_Exception 
	 */
	public function ewg(): float {
		// $ewg = 1/(1/($Qhfwp*/$Qhfwp)+0.1);
		return 1 / ( 1 / ( $this->Qhfwp() / $this->Qhfwp() ) + 0.1 );		
	}
}
